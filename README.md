# Laravel 11 + Docker + MySQL + Vite

## 📌 Prerequisites

Pastikan Anda telah menginstal:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

---

## 🚀 Cara Instalasi

### 1️⃣ Clone Repository

```sh
git clone https://github.com/username/repository.git
cd repository
```

### 2️⃣ Copy Environment File

```sh
cp .env.example .env
```

Lalu ubah konfigurasi database sesuai dengan `docker-compose.yml`:

```
DB_CONNECTION=mysql
DB_HOST=mysql_db
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3️⃣ Build dan Jalankan Docker

```sh
docker-compose up -d --build
```

Tunggu hingga semua container berjalan dengan baik.

### 4️⃣ Install Dependencies

```sh
docker exec -it laravel_app composer install
```

### 5️⃣ Generate Key

```sh
docker exec -it laravel_app php artisan key:generate
```

### 6️⃣ Migrate dan Seed Database

Jika perlu melakukan migration ulang run command ini

```sh
docker exec -it laravel_app php artisan migrate:fresh --seed
```

---

## 🔥 Akses Aplikasi

| Service     | URL                                            |
| ----------- | ---------------------------------------------- |
| Laravel App | [http://localhost:80](http://localhost:80)     |
| Vite (HMR)  | [http://localhost:5173](http://localhost:5173) |
| phpMyAdmin  | [http://localhost:8080](http://localhost:8080) |

---

## 🛑 Stop & Hapus Container

```sh
docker-compose down -v
```

---

## 🎯 Troubleshooting

1. **Database tidak terhubung?**
   - Pastikan MySQL sudah berjalan: `docker ps`
   - Cek logs: `docker logs -f mysql_db`
   - Restart container: `docker-compose restart mysql`

---

Sekarang Laravel 11 + Docker + MySQL + Vite sudah berjalan dengan lancar! 🚀
