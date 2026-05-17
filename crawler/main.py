import os
import time
import feedparser
import requests
import pymysql
import re
from datetime import datetime
from bs4 import BeautifulSoup
from email.utils import parsedate_tz, mktime_tz
from slugify import slugify # pip install python-slugify

# ==========================================
# 1. DATABASE CONNECTION MODULE
# ==========================================
def get_db_connection():
    """
    Thiết lập kết nối với Aiven MySQL sử dụng SSL cert.
    Các thông số lấy qua biến môi trường để đảm bảo tính bảo mật.
    """
    cert_path = os.path.join(os.path.dirname(__file__), '../storage/certs/ca.pem')
    
    return pymysql.connect(
        host=os.getenv('DB_HOST', 'mobiledata-mobilepro.c.aivencloud.com'),
        port=int(os.getenv('DB_PORT', 16827)),
        user=os.getenv('DB_USER', 'avnadmin'),
        password=os.getenv('DB_PASSWORD', 'your_password'),
        database=os.getenv('DB_NAME', 'defaultdb'),
        cursorclass=pymysql.cursors.DictCursor,
        ssl={'ca': cert_path}
    )

def url_exists(cursor, url):
    """Kiểm tra URL đã tồn tại chưa (Anti-duplicate)."""
    sql = "SELECT Article_ID FROM Article WHERE Original_URL = %s"
    cursor.execute(sql, (url,))
    return cursor.fetchone() is not None

# ==========================================
# 2. CRAWLER & HTML PARSER MODULE
# ==========================================
def fetch_and_clean_article(url):
    """
    Lấy chi tiết bài báo (full text) và làm sạch HTML
    Returns: content_html, clean_text (hoặc None nếu lỗi)
    """
    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status() 
        soup = BeautifulSoup(response.content, 'html.parser')
        
        content_div = soup.find('article', class_='fck_detail')
        if not content_div:
            content_div = soup.find('div', class_='fck_detail')
            
        if not content_div:
            return None, None
            
        for tag in content_div.find_all(['script', 'style', 'iframe']):
            tag.decompose()
            
        content_html = str(content_div)
        clean_text = ' '.join(content_div.get_text(strip=True).split())
        
        return content_html, clean_text

    except requests.RequestException as e:
        print(f"[-] Lỗi truy cập URL {url}: {e}")
        return None, None
    except Exception as e:
        print(f"[-] Lỗi parse HTML cho {url}: {e}")
        return None, None

def parse_publish_date(date_str):
    """Chuyển đổi ngày tháng."""
    try:
        dt = mktime_tz(parsedate_tz(date_str))
        return datetime.fromtimestamp(dt).strftime('%Y-%m-%d %H:%M:%S')
    except Exception:
        return datetime.now().strftime('%Y-%m-%d %H:%M:%S')

# ==========================================
# 3. MAIN RUNNER MODULE
# ==========================================
def run_crawler():
    rss_url = "https://vnexpress.net/rss/so-hoa.rss"
    print(f"[*] Đang parse RSS: {rss_url}")
    
    feed = feedparser.parse(rss_url)
    if not feed.entries:
        print("[-] Không lấy được dữ liệu. Vui lòng kiểm tra lại URL hoặc mạng.")
        return

    connection = None
    try:
        connection = get_db_connection()
        print("[*] Kết nối Database thành công!")
        
        total_inserted = 0
        
        for entry in feed.entries:
            title = entry.get('title', '')
            link = entry.get('link', '')
            pub_date_str = entry.get('published', '')
            summary = entry.get('summary', '')

            thumbnail_url = None
            if summary:
                soup_summary = BeautifulSoup(summary, 'html.parser')
                img_tag = soup_summary.find('img')
                if img_tag and img_tag.has_attr('src'):
                    thumbnail_url = img_tag['src']

            publish_date = parse_publish_date(pub_date_str)
            slug = slugify(title) if title else None

            with connection.cursor() as cursor:
                if url_exists(cursor, link):
                    print(f"[*] Đã tồn tại, bỏ qua: {title[:50]}...")
                    continue
            
            print(f"[+] URL mới, đang crawl chi tiết: {link}")
            content_html, clean_text = fetch_and_clean_article(link)
            
            if not content_html:
                print(f"[-] Không thể parse nội dung, bỏ qua: {link}")
                continue

            try:
                with connection.cursor() as cursor:
                    sql_article = """
                        INSERT INTO Article 
                        (Title, Slug, ThumbnailURL, Original_URL, PublishDate, Status) 
                        VALUES (%s, %s, %s, %s, %s, %s)
                    """
                    cursor.execute(sql_article, (title, slug, thumbnail_url, link, publish_date, 'published'))
                    article_id = cursor.lastrowid
                    
                    sql_content = """
                        INSERT INTO Article_Content 
                        (Article_ID, ContentHTML, CleanText) 
                        VALUES (%s, %s, %s)
                    """
                    cursor.execute(sql_content, (article_id, content_html, clean_text))
                
                connection.commit()
                total_inserted += 1
                print(f"   => Save THÀNH CÔNG: ID {article_id}")
                time.sleep(1) 
                
            except pymysql.MySQLError as e:
                connection.rollback()
                print(f"[-] ERROR DB cào/lưu bài {link}: {e}")
                
        print(f"[*] Hoàn thành! Đã lưu thêm {total_inserted} bài báo mới.")
                
    except pymysql.MySQLError as e:
        print(f"[-] Lỗi kết nối CSDL: {e}")
    except Exception as e:
        print(f"[-] Lỗi Crash Script: {e}")
    finally:
        if connection and connection.open:
            connection.close()
            print("[*] Đã đóng kết nối Database.")

if __name__ == '__main__':
    run_crawler()