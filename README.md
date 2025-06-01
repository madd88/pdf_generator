
# Api формирования PDF по шаблону



## Установка

1. Измените параметры в файле /env

```env
DB_HOST=
DB_NAME=
DB_USER=
DB_PASS=
PDF_STORAGE_RELATIVE="generated"
BASE_URL=
LOG_PATH=var/logs/app.log
TEMPLATES_PATH="templates"
PDF_STORAGE_PATH=public/generated
ASSETS_PATH=public/assets
```
2. Создайте таблицу в БД

```sql
CREATE TABLE generated_files (
    id VARCHAR(36) PRIMARY KEY, 
    filename VARCHAR(255) NOT NULL,     
    template VARCHAR(255) NOT NULL,    
    data JSON NOT NULL,     
    file_path VARCHAR(255) NOT NULL,     
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP )

```
## API Reference



#### Сгенерировать PDF

```http
  POST /generate
```

Geico пример запроса
```json
{
    "template": "geico_insurance",
    "name": "John Doe",
    "addressLine1": "123 Main St",
    "town": "Los Angeles",
    "state": "CA",
    "zip": "90001",
    "vehicleYear": "2020",
    "vehicleModel": "Toyota Camry",
    "vin": "4T1BF1FK7HU680211",
    "effectiveDate": "2023-06-15",
    "additionalDriver": "Jane Smith"
}
```


Hippo пример запроса
```json
{
    "template": "hippo_policy",
    "homeownerName": "John Doe Immanuln Cant",
    "homeownerStreet": "123 Main Street",
    "homeownerTown": "New York",
    "homeownerState": "NY",
    "homeownerZIP": "10001",
    "propertyAddress": "123 Main Street, New York, NY 10001",
    "builtYear": "1995",
    "squareFootage": "2500",
    "constructionType": "Frame",
    "creationDate": "2023-10-11"
}
```

Ein Letter LLC/INC пример запроса
```json
{
"template": "ein_letter",
  "businessName": "COCO BRAND LLC",
  "businessType": "Profit LLC", //или Profit Corp
  "businessAddress": "139 RUSSELL AVE ",
  "businessTown": "Jersy",
  "businessState": "LA",
  "businessZip": "44665",
  "incorporationDate": "2025-01-01",
  "ein": "85-1235478",
  "ownerName": "CARLY ELIZABETH NUNES"
}
```

Ответ сервера в json формате
```json
{
    "id": "683cd9df5065a",
    "status_url": "http://host1880171.hostland.pro/files/683cd9df5065a",
    "file_url": "http://host1880171.hostland.pro/generated/683cd9df5065a.pdf"
}
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | id сформированного файла |
| `status_url`| `string` | Ссылка на получене файла по id |
| `file_url`      | `string` | прямая ссылка на файл |

#### Get item

```http
  GET /files/${id}
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | **Required**. Id PDF файла |

Возвращает json
```json
{
    "id": "683cd9df5065a",
    "url": "http://host1880171.hostland.pro/generated/683cd9df5065a.pdf"
}
```
| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | id файла |
| `url`      | `string` | ссылка на файл |