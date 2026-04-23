# TechByte - Core System (Monorepo) 🚀

Hệ thống tổng hợp, tóm tắt và phân tích tin tức công nghệ tự động.
Repository này chứa toàn bộ mã nguồn của hệ thống Back-end phục vụ cho Mobile App và Data Pipeline thu thập dữ liệu.

## 📑 Mục lục

- [Tổng quan](#-tổng-quan)
- [Quick Start 5 phút](#-quick-start-5-phút)
- [Cấu trúc Repository](#-cấu-trúc-repository-monorepo)
- [Tech Stack](#-tech-stack)
- [Hướng dẫn Setup Local](#-hướng-dẫn-setup-local-cho-dev-team)
- [Quy trình vận hành dữ liệu](#-quy-trình-vận-hành-dữ-liệu)
- [Sơ đồ luồng hệ thống](#-sơ-đồ-luồng-hệ-thống)
- [Danh mục API chính (Draft)](#-danh-mục-api-chính-draft)

## 📌 Tổng quan

Dự án được chia làm 2 phân hệ hoạt động độc lập để tối ưu hiệu năng:

- `/backend`: RESTful API Server xây dựng bằng **Laravel 11**. Chịu trách nhiệm giao tiếp với App Android và xử lý logic người dùng.
- `/crawler`: Data Ingestion Pipeline xây dựng bằng **Python**. Chịu trách nhiệm cào tin tức, gọi AI (Gemini) và đẩy dữ liệu vào Database.
- `/.github/workflows`: Kịch bản tự động hóa (Cron job) chạy Crawler hàng ngày.
- `/docs`: Chứa tài liệu thiết kế hệ thống và sơ đồ cơ sở dữ liệu (ERD).

## ⚡ Quick Start 5 phút

### 1) Chạy API Server (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 2) Chạy Data Pipeline (Python)

```bash
cd ../crawler
python -m venv venv
```

Kích hoạt môi trường ảo:

Windows:

```bash
venv\Scripts\activate
```

Mac/Linux:

```bash
source venv/bin/activate
```

```bash
pip install -r requirements.txt
```

## 📂 Cấu trúc Repository (Monorepo)

```text
techbyte-backend/
├─ backend/            # Laravel API Server
├─ crawler/            # Python Data Pipeline
├─ .github/workflows/  # Cron workflow chạy crawler
└─ docs/               # Tài liệu hệ thống và ERD
```

## 🛠 Tech Stack

- **Back-end Framework:** Laravel 11 (PHP 8.2+)
- **Database:** PostgreSQL (Supabase) / MySQL Cloud
- **Data Crawler:** Python 3.10+ (BeautifulSoup, Requests)
- **AI Integration:** Google Gemini 2.5 Flash
- **CI/CD:** GitHub Actions

## ⚙️ Hướng dẫn Setup Local cho Dev Team

Để đảm bảo đồng bộ dữ liệu giữa các thành viên trong nhóm, vui lòng thực hiện đúng thứ tự sau.

### A. Setup API Server (Laravel)

1. Di chuyển vào thư mục backend:

```bash
cd backend
```

2. Cài đặt thư viện:

```bash
composer install
```

3. Thiết lập cấu hình môi trường:

```bash
cp .env.example .env
```

4. Mở file `.env` và điền thông số Database (liên hệ PM để lấy thông tin DB Cloud/Supabase).

5. Khởi tạo mã khóa ứng dụng:

```bash
php artisan key:generate
```

6. Đồng bộ Database (khởi tạo 15 bảng theo thiết kế và bơm dữ liệu mẫu vào máy local):

```bash
php artisan migrate --seed
```

7. Khởi động server:

```bash
php artisan serve
```

### B. Cấu hình Data Pipeline (Python)

1. Di chuyển vào thư mục crawler:

```bash
cd ../crawler
```

2. Tạo môi trường ảo (venv):

```bash
python -m venv venv
```

3. Kích hoạt môi trường ảo:

Windows:

```bash
venv\Scripts\activate
```

Mac/Linux:

```bash
source venv/bin/activate
```

4. Cài đặt thư viện cần thiết:

```bash
pip install -r requirements.txt
```

Lưu ý: file `requirements.txt` bao gồm `beautifulsoup4`, `requests`, `google-generativeai`, `mysql-connector-python`.

## ⚙️ Quy trình vận hành dữ liệu

1. GitHub Actions tự động kích hoạt hàng ngày để chạy script trong thư mục `/crawler`.
2. Script Python cào tin, gọi Gemini 2.5 Flash để tóm tắt và trích xuất specs.
3. Dữ liệu được lưu trực tiếp vào MySQL Cloud qua lệnh `INSERT/UPDATE`.
4. Laravel API truy xuất dữ liệu từ MySQL và trả về định dạng JSON cho Android App.

## 🧭 Sơ đồ luồng hệ thống

```mermaid
flowchart TD
	A[GitHub Actions Cron] --> B[Crawler Python]
	B --> C[Google Gemini 2.5 Flash]
	C --> D[(MySQL Cloud)]
	B --> D
	D --> E[Laravel 11 API]
	E --> F[Android App]
```

## 🔌 Danh mục API chính (Draft)

| Method | Endpoint                   | Mô tả                                  |
| ------ | -------------------------- | -------------------------------------- |
| POST   | `/api/auth/login`          | Đăng nhập lấy JWT Token                |
| GET    | `/api/articles`            | Danh sách bài báo (phân trang)         |
| GET    | `/api/articles/{id}`       | Chi tiết bài báo (nội dung tóm tắt)    |
| GET    | `/api/articles/{id}/specs` | JSON thông số kỹ thuật (để vẽ biểu đồ) |
| POST   | `/api/comments`            | Gửi bình luận mới                      |
