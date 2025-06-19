# 🛠️ sws-portfolio - Backend

**sws-portfolio.com**의 백엔드 저장소입니다. 이 프로젝트는 Laravel 기반으로 개발되었으며, 포트폴리오 사이트의 사용자 인증, 게시글 관리, 마이페이지 기능 등을 처리합니다.

---

## 🚀 주요 기능

* 🔐 JWT 기반 사용자 인증 및 로그인
* 📝 게시글 CRUD (PostController)
* RESTful API 구조 및 `auth:api` 미들웨어 적용

---

## 📁 주요 디렉토리 구조

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           └── PostController.php       # 게시글 관련 API
routes/
├── api.php         # API 라우팅 및 인증 적용
├── web.php         # 기본 웹 라우팅
├── console.php     # 콘솔 명령 라우팅
config/             # Laravel 설정 파일
public/             # 배포용 엔트리 경로
resources/views     # Blade 템플릿
```

---

## ⚙️ 실행 방법

### 1. 클론 및 설치

```bash
git clone https://github.com/사용자명/sws-portfolio-backend.git
cd sws-portfolio-backend
composer install
```

### 2. 환경 설정

```bash
cp .env.example .env
php artisan key:generate
```

`.env` 파일 내 다음 항목 설정 필요:

* 데이터베이스 정보
* JWT 비밀키
* 기타 서버 환경 변수

### 3. 마이그레이션 및 실행

```bash
php artisan migrate
php artisan serve
```

---

## 📬 API 예시

### POST `/api/login`

* 사용자 로그인 및 JWT 토큰 반환

### CRUD `/api/posts`

* 게시글 등록, 수정, 삭제, 조회

---

## 🧪 테스트

```bash
php artisan test
```

---

## 👨‍💻 개발자

* 손우성 ([@captain9802](https://github.com/captain9802))

---
