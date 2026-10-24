============================================================
JPM MASTER ENGINEERING QA & RELEASE STANDARD
============================================================

DOCUMENT PURPOSE
----------------

Đây là tiêu chuẩn kiểm thử, audit, verification, debugging,
integration và release chính thức của toàn bộ dự án JPM.

Tài liệu này là SOURCE OF TRUTH cho mọi hoạt động:

- Page audit
- Feature audit
- Module audit
- Entity audit
- API audit
- Frontend audit
- Backend audit
- Database audit
- Business logic audit
- Security audit
- Performance audit
- Integration audit
- Regression test
- Pre-release audit
- Go-live certification

Mọi AI Agent / Developer / QA Agent khi được yêu cầu kiểm tra
JPM PHẢI đọc và tuân thủ tài liệu này trước khi bắt đầu.

Không được tự ý giảm phạm vi kiểm tra.

Không được tự suy luận rằng một phần đúng thì phần còn lại đúng.

Không được dùng một số lượng test nhỏ để suy rộng thành PASS
cho toàn bộ hệ thống.

============================================================
0. CORE PHILOSOPHY: PRACTICAL REAL-WORLD FIRST (NGUYÊN TẮC THỰC CHIẾN)
============================================================

BẮT BUỘC: Mọi AI Agent, Kỹ sư phần mềm và Quản lý dự án khi phân tích,
thiết kế, đánh giá hoặc triển khai bất kỳ tính năng nào của JPM
PHẢI đứng từ vị thế của CHUYÊN GIA KỸ THUẬT & THỰC TIỄN KINH DOANH
để nhận xét, phản biện và tối ưu:

1. THỰC CHIẾN HƠN LÝ THUYẾT:
   - Tuyệt đối không máy móc làm theo các tài liệu cũ hay yêu cầu rườm rà,
     hàn lâm, xa rời thực tế kinh doanh ngoài đời.
   - Luôn đặt câu hỏi: "Người bán hàng và khách hàng ngoài đời có thực sự
     dùng tính năng này như vậy không? Có cách nào tinh gọn hơn không?"

2. TINH GỌN (LEAN OPERATIONS):
   - Cắt bỏ mọi bước trung gian quan liêu (như ép khách bấm link xác nhận
     khi đã chốt Zalo, hoặc tạo PO lẻ tẻ từng đơn khiến ngập rác hệ thống).
   - Tối đa hóa tốc độ chốt đơn, gom nhu cầu nhập hàng thông minh,
     giảm thiểu thao tác thừa cho Admin và Khách hàng.

3. TRÁCH NHIỆM PHẢN BIỆN CHỦ ĐỘNG:
   - Khi phát hiện luồng phi lý hoặc rườm rà, Agent PHẢI thẳng thắn chỉ ra
     bất cập và đề xuất giải pháp thực tế tối ưu trước khi thực hiện.

4. CHUẨN ĐẶT URL HỆ THỐNG (RESTFUL & KEBAB-CASE CONVENTION):
   - Mọi URL (Client & Admin) BẮT BUỘC dùng chữ thường, phân tách bằng dấu gạch ngang (kebab-case).
   - Tuyệt đối KHÔNG viết dính liền (CẤM: `khachhang`, `loaidongco`, `danhmucsanpham`).
   - Tuyệt đối KHÔNG lặp từ vô nghĩa (CẤM: `/khachhang/khachhang`, `/sanpham/sanpham`, `/kho/kho`).
   - Cấu trúc URL Admin chuẩn tinh gọn: `/admin/[thuc-the-chinh]` (e.g. `/admin/khach-hang`, `/admin/don-hang`, `/admin/san-pham`, `/admin/ton-kho`).
   - Mọi URL khi thay đổi BẮT BUỘC phải giữ lại redirect backward-compatible để không bao giờ bị lỗi 404.

5. CHUẨN GIAO DIỆN TOÀN HỆ THỐNG - ADMIN, STAFF & CLIENT (BOOTSTRAP 5 & KIẾN TRÚC CSS RẠCH RÒI):
   - **Triết lý thống nhất:** Mọi tác nhân (Admin, Staff, Client) đều viết markup bằng **100% Bootstrap 5 chuẩn**, gọn gàng, ngữ nghĩa cao, tối giản.
   - **Phân định rạch ròi kiến trúc CSS: Toàn cục (`main.css`/`demo.css`) vs Cục bộ (`<style scoped>`):**
     - **`main.css` (Client) & `demo.css` (Admin):** Đóng vai trò như **Framework CSS dùng chung toàn website** (giống như Bootstrap core).
       + **CHỈ CHỨA:** Token màu sắc/biến CSS toàn cục, tùy biến/ghi đè component Bootstrap chính quy (`.btn-primary`, `.btn-danger`, `.badge`, `.card`), tiện ích dùng chung (`.text-truncate-2`, `.text-truncate-3`), component chung (`.product-card`, `.btn-cart-action`).
       + **TUYỆT ĐỐI CẤM:** Không đưa CSS mang tính cục bộ của riêng 1 trang/section vào `main.css` (CẤM: class cục bộ theo kiểu `.news-section .card-title`, `.news-img-thumb`... hoặc ép số dòng hiển thị line-clamp của 1 block trên trang vào `main.css` vì sẽ gây ô nhiễm và xung đột khi sang trang khác).
     - **`<style scoped>` trong component Vue:** Dành riêng cho **tinh chỉnh đặc thù của chính trang đó**:
       + Số dòng hiển thị nội dung (`-webkit-line-clamp`) của từng block trên trang (ví dụ tóm tắt bài viết Trang chủ 3 dòng, trang khác 2 dòng hoặc full).
       + Kích thước ảnh, thumbnail hoặc tỉ lệ khung hình (aspect-ratio) đặc thù của block trong trang đó.
       + Khoảng cách lề `margin`/`padding` cục bộ và sắp xếp layout nội bộ của khối trong trang đó.
       + Được Vue tự động cách ly tuyệt đối bằng mã hash `data-v-xxx`, không bao giờ ảnh hưởng sang trang khác.
   - **Quy định nghiêm ngặt về Inline Style (`style="..."`):**
     - Inline `style=""` **DUY NHẤT** được chấp nhận cho kích thước vật lý hoặc thông số động (`width`, `height`, tọa độ sơ đồ rã máy...).
     - Tuyệt đối cấm dùng inline `style=""` để can thiệp màu sắc, viền bo góc, bóng mờ hay căn lề.
   - **Chuẩn chi tiết theo từng phân hệ:**
     - Admin/Staff: Tuân thủ [09_Admin_Staff_UI_Standard.md](09_Admin_Staff_UI_Standard.md), thống nhất theo Pattern A (Full Table + Modal) hoặc Pattern B (Chia đôi 4/8 Form + Table).
     - Client: Tuân thủ [10_Client_UI_Standard.md](10_Client_UI_Standard.md), khung section chuẩn TheEvent, thẻ card bo góc nhẹ, bảng giỏ hàng thoáng đãng.

============================================================


============================================================
1. PROJECT CONTEXT
============================================================

JPM là hệ thống ERP + E-Commerce chuyên về:

- Marine engine
- Marine equipment
- Spare parts
- Parts catalog
- Engine model
- Engine diagram
- Replacement parts
- Inventory
- Procurement
- Sales
- Customer
- Garage
- Quote
- Order
- CMS
- IAM
- Administration

Kiến trúc hiện tại:

BACKEND
- Laravel 12
- PHP 8.2+
- REST API
- MySQL
- Sanctum
- Spatie Permission
- Domain-Driven Design
- app/Domain
- Service-oriented business logic
- Eloquent ORM
- FormRequest validation
- API Resources
- Transactions
- SoftDeletes
- Idempotency
- Pessimistic locking

FRONTEND
- Vue 3
- Vite
- Composition API
- Pinia
- Axios
- TanStack Vue Query
- Vue Router
- SweetAlert2 / Toast
- ApexCharts
- Swiper
- AOS
- các component/library thực tế đang tồn tại trong source

PROJECT STRUCTURE
- FE_JPM
- BE_JPM

DATABASE
- MySQL
- db_jpm

Không được giả định công nghệ vẫn giữ nguyên.
Luôn kiểm tra source thực tế trước khi audit.

============================================================
2. CORE PRINCIPLE
============================================================

JPM phải được kiểm tra như MỘT HỆ THỐNG DUY NHẤT.

Không audit FE, BE hoặc Database như những hệ thống độc lập.

Mọi feature phải được trace:

USER
 ↓
UI
 ↓
EVENT
 ↓
FUNCTION
 ↓
STATE
 ↓
REQUEST
 ↓
API
 ↓
ROUTE
 ↓
AUTH / PERMISSION
 ↓
CONTROLLER
 ↓
VALIDATION
 ↓
SERVICE
 ↓
DOMAIN LOGIC
 ↓
MODEL / RELATIONSHIP
 ↓
DATABASE QUERY
 ↓
DATABASE
 ↓
RESPONSE
 ↓
API RESOURCE / TRANSFORM
 ↓
FRONTEND STATE / CACHE
 ↓
FINAL UI

Đây là DATA LINEAGE bắt buộc.

Nếu bất kỳ tầng nào sai:
FEATURE = FAIL.

============================================================
3. AUDIT MODES
============================================================

Khi user yêu cầu audit:

A. PAGE AUDIT

Ví dụ:
"Kiểm tra page Khách hàng"

Phải kiểm tra:

- Page UI
- route
- components
- buttons
- forms
- events
- functions
- API
- backend
- DB
- state
- cache
- permission
- business logic
- error handling
- related entities
- Client impact
- Admin impact

Không được chỉ kiểm tra file .vue.

--------------------------------

B. ENTITY / ACTOR AUDIT

Ví dụ:
"Kiểm tra Customer"

Phải audit toàn bộ lifecycle:

CREATE
READ
UPDATE
DELETE
STATUS
AUTH
PROFILE
PASSWORD
AVATAR
RELATIONSHIPS
ORDERS
QUOTES
GARAGE
HISTORY
PERMISSIONS
ADMIN
CLIENT
DATABASE

Customer phải được trace:

Client
↔ API
↔ IAM
↔ Customer
↔ User
↔ Database
↔ Admin
↔ Order
↔ Quote
↔ Garage

--------------------------------

C. MODULE AUDIT

Ví dụ:
"Kiểm tra Order"

Phải kiểm tra:

Client Order
Admin Order
Cart
Checkout
Quote conversion nếu có
Pricing
Inventory
Status machine
Cancellation
Restore inventory
History
Payment
Customer relation
Concurrency
Idempotency
Database
API
UI
Cache

--------------------------------

D. FULL PROJECT AUDIT

Phải kiểm tra:

ALL CLIENT
ALL ADMIN
ALL API
ALL BACKEND
ALL DOMAIN
ALL DATABASE
ALL BUSINESS LOGIC
ALL CROSS-MODULE INTEGRATION
ALL SECURITY
ALL TECHNOLOGY
ALL RELEASE RISKS

Không được bỏ qua bất kỳ layer nào.

============================================================
4. INVENTORY BEFORE TESTING
============================================================

Trước khi audit, phải inventory source thực tế.

Không đoán.

Không dùng số cũ nếu source đã thay đổi.

Phải xác định:

CLIENT PAGES
ADMIN PAGES
ROUTES
COMPONENTS
STORES
COMPOSABLES
API SERVICES
API ENDPOINTS
CONTROLLERS
FORM REQUESTS
SERVICES
DOMAIN SERVICES
MODELS
RESOURCES
POLICIES
MIDDLEWARE
EVENTS
JOBS
NOTIFICATIONS
MIGRATIONS
DATABASE TABLES
SEEDERS
CONFIG
ENVIRONMENT
STORAGE
TESTS

Ví dụ:

TOTAL CLIENT PAGES = X
TOTAL ADMIN PAGES = X
TOTAL API ENDPOINTS = X
TOTAL CONTROLLERS = X
TOTAL SERVICES = X
TOTAL MODELS = X
TOTAL TABLES = X

Các con số phải lấy từ source hiện tại.

============================================================
5. PAGE-BY-PAGE RULE
============================================================

Mỗi page phải được kiểm tra riêng.

Không được viết:

"27 page còn lại PASS."

Không được:

"các page khác tương tự."

Không được:

"module này PASS nên page liên quan PASS."

Mỗi page phải có:

PAGE
ROUTE
COMPONENT
FUNCTION COUNT
BUTTON COUNT
API COUNT
PASS
FAIL
BLOCKED
UNTESTED

Ví dụ:

Customer.vue

Functions: 12
PASS: 11
FAIL: 1
BLOCKED: 0
UNTESTED: 0

Result:
FAIL

Chỉ khi tất cả critical functionality PASS:
Page = PASS.

============================================================
6. BUTTON-BY-BUTTON RULE
============================================================

Mỗi button/action phải được kiểm tra.

Bao gồm:

- normal button
- icon button
- dropdown action
- table action
- row action
- bulk action
- submit
- cancel
- delete
- restore
- status toggle
- reset password
- upload
- remove
- preview
- export
- import
- approve
- reject
- confirm
- special business action

Trace:

CLICK
 ↓
EVENT
 ↓
HANDLER
 ↓
FUNCTION
 ↓
REQUEST
 ↓
API
 ↓
BACKEND
 ↓
DATABASE
 ↓
RESPONSE
 ↓
STATE
 ↓
UI

Không được đánh PASS chỉ vì button có @click.

============================================================
7. FORM AUDIT
============================================================

Mỗi form phải kiểm tra:

- open
- close
- create
- edit
- submit
- cancel
- reset
- required
- nullable
- type
- min
- max
- format
- unique
- duplicate
- empty
- invalid
- server validation
- 422
- 409
- 500
- loading
- double submit
- stale state
- reset state
- edit existing record
- deleted record đang edit

Frontend validation phải khớp backend validation.

Backend vẫn phải bảo vệ business rule dù frontend bị bypass.

============================================================
8. CRUD AUDIT
============================================================

Mỗi entity có CRUD phải kiểm tra:

CREATE
READ
UPDATE
DELETE

và nếu có:

RESTORE
STATUS
APPROVE
REJECT
CANCEL
ARCHIVE
BULK ACTION

Không được:

Create + Read + Update PASS
→ Entity PASS

Nếu Delete FAIL:

Entity = FAIL.

============================================================
9. SEARCH / FILTER / SORT / PAGINATION
============================================================

Mỗi list page phải kiểm tra:

Search
Filter
Sort
Pagination
Reset filter

Kiểm tra:

UI
 ↓
query params
 ↓
API
 ↓
Backend
 ↓
DB
 ↓
pagination metadata
 ↓
UI

Phải kiểm tra:

- page
- per_page
- total
- current_page
- last_page
- result ordering
- empty result
- no result
- reset state
- filter combination

============================================================
10. STATE MANAGEMENT
============================================================

Kiểm tra:

Pinia
Vue Query
local state
component state
composables
Axios state

Sau:

CREATE
UPDATE
DELETE
STATUS
UPLOAD
REMOVE
PASSWORD
APPROVE
CANCEL

phải kiểm tra:

- query invalidation
- refetch
- cache
- optimistic update
- rollback
- stale data
- duplicate data
- list order
- detail state
- edit form state

Phải phát hiện:

LIST JUMPING
STALE FORM
STALE DETAIL
STALE CACHE
DUPLICATE RECORD
DELETED RECORD STILL VISIBLE
WRONG PAGINATION
WRONG ORDER

Không dùng setTimeout để che race condition.

============================================================
11. API CONTRACT AUDIT
============================================================

Mỗi endpoint phải kiểm tra:

METHOD
URL
AUTH
PERMISSION

REQUEST:

PATH
PARAMS
QUERY
BODY
FORM-DATA
HEADERS

RESPONSE:

STATUS
DATA
MESSAGE
ERRORS
PAGINATION
RELATIONSHIPS

Đối chiếu:

FRONTEND EXPECTATION
vs
BACKEND ACTUAL RESPONSE

Phải kiểm tra:

snake_case
camelCase
ID type
number/string
boolean
null/""
date
datetime
decimal
image URL
nested objects
relationships
pagination

Nếu mismatch:
FAIL.

============================================================
12. DATABASE SCHEMA AUDIT
============================================================

Không được chỉ đọc migration.

Phải đối chiếu DATABASE THỰC TẾ.

Kiểm tra:

SHOW TABLES
SHOW COLUMNS
SHOW CREATE TABLE
INDEXES
FOREIGN KEYS
UNIQUE
NULLABLE
DEFAULT
TYPE
PRECISION
SOFT DELETE
CONSTRAINTS

Database thực tế là source of truth khi runtime testing.

============================================================
13. MODEL ↔ DATABASE RELATIONSHIP
============================================================

Kiểm tra toàn bộ:

hasOne
hasMany
belongsTo
belongsToMany
morphOne
morphMany

Đối chiếu:

MODEL
 ↓
RELATIONSHIP
 ↓
FOREIGN KEY
 ↓
MIGRATION
 ↓
ACTUAL DATABASE COLUMN

Không được giả định Laravel naming convention.

Ví dụ JPM có convention:

id_khach_hang
id_don_hang
id_kho
id_san_pham
id_phien_ban

Trong khi Laravel thường suy luận:

khach_hang_id
don_hang_id
kho_id
san_pham_id
phien_ban_id

Do đó mọi relationship/query phải được xác minh thực tế.

============================================================
14. KNOWN JPM NAMING-CONVENTION RISK
============================================================

Đây là một TEST CATEGORY BẮT BUỘC.

Search toàn bộ:

*_id

trong:

Models
Services
Controllers
Resources
Requests
Queries
Scopes
Validation
Relationships
Joins
Repositories

Nhưng KHÔNG được search-replace mù.

Phải phân biệt:

API CONTRACT FIELD

và

DATABASE COLUMN.

Ví dụ:

API:
san_pham_id

DATABASE:
id_san_pham

có thể hợp lệ nếu Controller mapping:

san_pham_id
→ id_san_pham

Đây là mapping, không phải bug.

============================================================
15. RESOURCE AUDIT
============================================================

Tất cả API Resource phải kiểm tra:

- field tồn tại
- relationship tồn tại
- property tồn tại
- nested Resource
- null handling
- deleted relationship
- serialization
- N+1 risk
- response contract

Phải runtime serialize các Resource critical.

Không chỉ compile.

============================================================
16. BUSINESS LOGIC AUDIT
============================================================

Không được giới hạn CRUD.

Phải kiểm tra nghiệp vụ thực tế.

ORDER:

- pricing
- server-side price
- inventory
- reservation
- deduction
- cancellation
- restoration
- status
- history
- concurrency
- idempotency
- customer relation

QUOTE:

- create
- update
- approve
- reject
- conversion
- conversion to order
- price
- quantity
- customer
- duplicate conversion

INVENTORY:

- stock
- deduction
- restoration
- warehouse
- stock ledger
- concurrency
- negative stock

CUSTOMER:

- registration
- login
- profile
- status
- password
- avatar
- orders
- quotes
- garage

MARINE CATALOG:

Brand
→ Engine Type
→ Engine Model
→ Diagram
→ Part
→ Replacement

GARAGE:

Customer
→ Engine
→ Model
→ Compatibility
→ Parts
→ History

PROCUREMENT:

Supplier
→ Purchase
→ Purchase Detail
→ Inventory

============================================================
17. PRICE SECURITY
============================================================

Never trust:

gia_ban
price
total
discount
subtotal

from frontend.

Server must calculate authoritative values from database/business rules.

Test:

normal payload
tampered price
tampered total
tampered discount
duplicate request

Expected:
server ignores/rejects manipulated values.

============================================================
18. INVENTORY CONCURRENCY
============================================================

Test:

2 users
same product
limited stock
simultaneous purchase

Verify:

transaction
lockForUpdate
stock consistency
no negative stock
no duplicate deduction

Cancellation:

order
→ cancel
→ restore stock

Repeated cancellation:

must not restore stock twice.

============================================================
19. IDEMPOTENCY
============================================================

Critical mutations must be safe against duplicate requests.

Test:

same request
same idempotency key
different payload same key
concurrent requests

Expected:

same key + same payload:
safe/idempotent

same key + different payload:
409 conflict

No duplicate order.
No duplicate inventory movement.
No duplicate financial/business mutation.

============================================================
20. AUTHENTICATION & AUTHORIZATION
============================================================

Test:

Guest
Customer
Staff
Admin
Super Admin
different permissions

Verify:

frontend guard
backend middleware
policy
permission
API authorization

Never trust frontend-only permission.

Test:

401
403
IDOR
cross-user access
wrong ID
missing token
expired token

============================================================
21. CUSTOMER / USER DATA ISOLATION
============================================================

User A must not:

read User B
edit User B
delete User B
read User B order
read User B quote
read User B garage

unless explicitly authorized.

Test at API level, not only UI.

============================================================
22. FILE / IMAGE AUDIT
============================================================

Every upload feature:

Upload
Preview
Replace
Remove
Cancel
Save
Delete

Test:

new image
existing image
remove existing
replace existing
invalid file
large file
missing file
null
empty string

Verify:

frontend
→ FormData
→ API
→ validation
→ storage
→ database
→ response
→ UI

============================================================
23. ERROR HANDLING
============================================================

Test:

400
401
403
404
409
422
500

Never allow unexpected raw database errors in normal production UX.

Examples:

SQLSTATE
Unknown column
Unknown table
Undefined property
Undefined relationship
Foreign key violation

must be investigated.

Business conflicts should be mapped appropriately, usually:

409 Conflict

Validation:

422

Unauthorized:

401

Forbidden:

403

Not found:

404

============================================================
24. CROSS-MODULE INTEGRATION
============================================================

Every important entity must be tested in both directions.

CLIENT
→ DATABASE
→ ADMIN

ADMIN
→ DATABASE
→ CLIENT

Examples:

Client Register
→ Customer
→ Admin Customer

Client Order
→ Database
→ Admin Order

Client Quote
→ Database
→ Admin Quote

Client Garage
→ Database
→ Admin Garage

Admin Brand
→ Database
→ Client Brand

Admin Part
→ Database
→ Client Part

Admin Inventory
→ Database
→ Client availability

Admin Order Status
→ Database
→ Client Order Status

If one side works but the other does not:
FAIL.

============================================================
25. CLIENT PROTECTION
============================================================

When auditing Admin, NEVER modify Client blindly.

When auditing Client, NEVER modify Admin blindly.

If shared backend changes:

1. identify all consumers
2. identify contract changes
3. test Admin
4. test Client
5. test database
6. test integration

A fix that makes Admin PASS but breaks Client is NOT a valid fix.

A fix that makes Client PASS but breaks Admin is NOT a valid fix.

============================================================
26. SECURITY AUDIT
============================================================

Check:

- authentication
- authorization
- IDOR
- privilege escalation
- mass assignment
- validation bypass
- file upload
- sensitive data exposure
- unauthorized API
- parameter tampering
- price manipulation
- quantity manipulation
- status manipulation
- customer data access
- admin data access

Never rely solely on frontend security.

============================================================
27. PERFORMANCE / TECHNOLOGY AUDIT
============================================================

Check:

Frontend:

- unnecessary API requests
- duplicate requests
- excessive rerender
- cache misuse
- bundle problems
- image loading
- memory leaks
- expensive watchers
- pagination

Backend:

- N+1
- missing indexes
- excessive queries
- inefficient joins
- transaction scope
- lock scope
- unnecessary eager loading
- expensive resources

Database:

- indexes
- foreign keys
- unique constraints
- query plans where relevant
- large table risks
- inefficient queries

Do not optimize prematurely.

First establish correctness.

============================================================
28. CONFIGURATION / ENVIRONMENT
============================================================

Check:

- .env usage
- API base URL
- storage URL
- CORS
- Sanctum
- filesystem
- APP_URL
- frontend environment
- production environment
- debug mode
- logging
- queue
- cache
- session
- mail
- database

No hardcoded:

127.0.0.1
localhost
development-only URL
development credentials

unless intentionally isolated to local development.

============================================================
29. DEPLOYMENT READINESS
============================================================

Before production:

Frontend:

- build succeeds
- environment variables correct
- routes correct
- API URL correct
- storage URL correct
- no hardcoded local host
- no console errors
- no debug UI
- assets load

Backend:

- production config
- APP_DEBUG=false
- migrations
- storage link
- permissions
- cache
- queue
- scheduler if used
- CORS
- Sanctum
- logging
- error handling

Database:

- migrations reproducible
- constraints correct
- indexes
- foreign keys
- seed requirements
- backup strategy

Security:

- secrets not committed
- credentials not hardcoded
- production debug disabled
- unauthorized endpoints blocked

============================================================
30. TEST DATA SAFETY
============================================================

Do not destroy real production data during testing.

Use isolated test data.

Clearly distinguish:

TEST DATA
vs
REAL DATA

Before destructive test:

- identify dependencies
- verify environment
- verify record is test data

============================================================
31. DO NOT USE FAKE PASS
============================================================

Never report PASS because:

- route exists
- API returns 200
- code compiles
- component renders
- static scan passes
- function exists
- endpoint exists

PASS requires evidence appropriate to the claim.

Examples:

API PASS:
actual request + expected response

Database PASS:
actual DB state/query

UI PASS:
actual UI interaction + final UI state

Integration PASS:
multiple layers verified together

Security PASS:
unauthorized case tested and rejected

============================================================
32. TEST STATUS DEFINITIONS
============================================================

PASS:

Function was actually tested and expected behavior was verified.

FAIL:

Function was tested and actual behavior differs from expected.

BLOCKED:

Cannot test because required capability/environment/tool is unavailable.

UNTESTED:

Function has not been tested.

NOT APPLICABLE:

Feature genuinely does not exist or does not apply.

Never convert:

BLOCKED → PASS

UNTESTED → PASS

Static PASS → Runtime PASS

API PASS → UI PASS

============================================================
33. EVIDENCE STANDARD
============================================================

Every important PASS/FAIL should have evidence.

Evidence can include:

- source file
- function
- route
- request
- response
- SQL
- database state
- migration
- query
- screenshot
- browser result
- test script output
- log
- stack trace

For bugs:

BUG ID
PAGE
FUNCTION
ACTION
REQUEST
API
BACKEND
DATABASE
EXPECTED
ACTUAL
ROOT CAUSE
IMPACT
FIX
RETEST
RESULT

============================================================
34. BUG ROOT-CAUSE RULE
============================================================

Never stop at symptom.

Example:

Symptom:
Delete Customer gives SQL 500.

Do not report only:

"Delete Customer broken."

Trace:

UI
→ request
→ controller
→ service
→ relationship
→ query
→ database

Root cause:

Code uses:
khach_hang_id

Database contains:
id_khach_hang

Then search for the same systemic pattern across the entire project.

A discovered bug pattern must trigger a GLOBAL PATTERN SEARCH.

============================================================
35. REGRESSION RULE
============================================================

Every fix requires:

FAIL
→ ROOT CAUSE
→ FIX
→ RETEST
→ REGRESSION

If shared code changes:

test every consumer.

Example:

Backend API contract changed.

Then test:

Client
+
Admin
+
Database
+
Integration

============================================================
36. NO BLIND GLOBAL REPLACEMENT
============================================================

Scripts that automatically modify many files must be audited.

Never blindly replace:

khach_hang_id
→ id_khach_hang

without checking context.

Potential contexts:

API field
DB column
relationship
resource
validation
query
request
response
frontend contract

Every automated global fix must be:

- reviewed
- diffed
- tested
- regression tested

============================================================
37. FULL PROJECT RELEASE GATES
============================================================

JPM cannot be declared GO if any of these remain:

CRITICAL FAIL
DATA INTEGRITY FAIL
SECURITY FAIL
BUSINESS LOGIC FAIL
DATABASE MISMATCH
BROKEN API CONTRACT
BROKEN ADMIN FUNCTION
BROKEN CLIENT FUNCTION
BROKEN CROSS-MODULE FLOW
UNRESOLVED CRITICAL REGRESSION

If UI browser testing is unavailable:

SYSTEM = BLOCKED FOR UI CERTIFICATION

not GO.

============================================================
38. FINAL AUDIT REPORT FORMAT
============================================================

# JPM FULL AUDIT REPORT

## A. PROJECT INVENTORY

Client Pages:
Admin Pages:
Components:
Routes:
API Endpoints:
Controllers:
FormRequests:
Services:
Domain Services:
Models:
Resources:
Database Tables:
Migrations:

## B. TEST COVERAGE

Pages:
Functions:
Buttons:
Forms:
APIs:
Database Operations:
Business Flows:

## C. RESULT

PASS:
FAIL:
BLOCKED:
UNTESTED:
N/A:

## D. PAGE-BY-PAGE

Page 01:
Functions:
PASS:
FAIL:
BLOCKED:

Page 02:
...

Never use:
"remaining pages PASS"

## E. ENTITY MATRIX

| Entity | Client | Admin | API | Backend | DB | Integration | Security | Result |

## F. BUSINESS FLOW MATRIX

| Flow | Client | API | Backend | DB | Admin | Result |

## G. BUGS

BUG-001
...

## H. ROOT CAUSES

Group by:

Frontend
Backend
Database
API Contract
Business Logic
State
Cache
Security
Integration
Performance
Configuration
Deployment

## I. FIXES

For each:

Before
Root Cause
Fix
After
Regression

## J. RELEASE READINESS

Frontend:
Backend:
Database:
Security:
Integration:
Performance:
Deployment:
UI:

## K. FINAL VERDICT

GO
NOT GO
BLOCKED

============================================================
39. GO CRITERIA
============================================================

GO chỉ khi:

- tất cả critical pages verified
- tất cả critical functions verified
- tất cả critical buttons verified
- CRUD verified
- API verified
- Database verified
- Business logic verified
- Client/Admin integration verified
- Security verified
- Regression verified
- Deployment verified
- no critical FAIL
- no unresolved critical BLOCKED
- no untested critical functionality

Nếu browser UI chưa được test:

Không được GO.

============================================================
40. PAGE-SPECIFIC AUDIT MUST STILL CHECK DEPENDENCIES
============================================================

Nếu user yêu cầu:

"Kiểm tra page Khách hàng"

KHÔNG được chỉ kiểm tra:

KhachHang.vue

Phải kiểm tra các dependency cần thiết:

Customer
↔ User
↔ Auth
↔ Order
↔ Quote
↔ Garage
↔ Database
↔ API
↔ Permissions

Nhưng phạm vi phải tập trung vào Customer.

Nếu dependency phát hiện lỗi ảnh hưởng Customer:
phải report.

============================================================
41. ENTITY-SPECIFIC AUDIT MUST CHECK ALL SURFACES
============================================================

Nếu user yêu cầu:

"Kiểm tra Customer"

phải kiểm tra:

Client Customer
Admin Customer
API Customer
User relationship
Order relationship
Quote relationship
Garage relationship
Database
Auth
Permission
Password
Avatar
Status
History
Delete rules
Business rules

============================================================
42. FULL PROJECT AUDIT MUST START FROM ZERO ASSUMPTIONS
============================================================

Khi user nói:

"Audit toàn bộ dự án"

Không được tin:

- audit cũ
- report cũ
- PASS cũ
- số lượng page cũ
- claim "100%"
- claim "Enterprise Grade"

Các report cũ chỉ là historical evidence.

Source code + actual DB + runtime behavior là evidence hiện tại.

============================================================
43. KNOWN HISTORICAL JPM BUG PATTERNS
============================================================

Các pattern sau phải luôn được coi là high-risk regression categories:

1. Price manipulation
2. Inventory race condition
3. Missing inventory restoration
4. Duplicate cancellation
5. Idempotency mismatch
6. Same-key/different-body requests
7. Hardcoded storage URL
8. Optimistic list jumping
9. Stale edit form after delete
10. Missing reset-password route
11. Missing avatar removal
12. Auto-code incorrectly exposed to user
13. Auto-code editable on update
14. Frontend/backend validation mismatch
15. Database FK naming mismatch
16. Eloquent relationship FK mismatch
17. Resource property mismatch
18. API request/response contract mismatch
19. Client/Admin state desynchronization
20. Raw SQL error exposed to UI
21. Authorization mismatch
22. IDOR
23. Shared backend change breaking Client
24. Shared backend change breaking Admin
25. UI action existing without working backend endpoint
26. Backend endpoint existing but frontend calling wrong route/method
27. Delete dependency not handled
28. Soft-delete inconsistency
29. Duplicate API request
30. Cache invalidation failure

Every full audit must explicitly check these categories.

============================================================
44. FINAL RULE
============================================================

The purpose of this document is NOT to generate a nice audit report.

The purpose is:

FIND THE BUGS.

PROVE THE BUGS.

FIND THE ROOT CAUSE.

FIX THE ROOT CAUSE.

REGRESSION TEST.

VERIFY THE ENTIRE SYSTEM.

ONLY THEN CERTIFY.

Never optimize the report for PASS rate.

Never hide uncertainty.

Never convert BLOCKED into PASS.

Never convert UNTESTED into PASS.

Never assume.

Never skip a page.

Never skip a button.

Never skip a function.

Never skip a database relationship.

Never skip an API contract.

Never skip business logic.

Never skip Client/Admin integration.

Never skip security.

Never skip deployment readiness.

JPM is considered production-ready only when the evidence
supports that conclusion.

============================================================
END OF JPM MASTER ENGINEERING QA & RELEASE STANDARD
============================================================
