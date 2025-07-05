
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
Пример Medical

```json
{
  "template": "medical",
  "name": "Имя Фамилия",
  "dob": "2000-01-01",
  "streetAddress": "ул. Примерная, д. 1",
  "town": "Москва",
  "state": "МО",
  "zip": "123456",
  "phoneNumber": "+7 (999) 123-45-67",
  "email": "user@example.com",
  "nameAdd": "Имя Фамилия",
  "phoneNumberAdd": "+7 (999) 123-45-67",
  "appointmentDate": "2025-07-15",
  "cause": "1",
  "excuseFrom": "Work",
  "excuseUntil": "2025-07-20",
  "weight": 75.5
}
```

Пример Invoice

```json
{
  "template" : "invoice",
  "notes": "<p>Thank you for your business!</p> <p>This invoice is due within 30 days from the issue date. Pleasemake payment using one of the methodsbelow.Please include the invoice number in the payment eference</p>",
  "business.name": "Acme Corporation",
  "business.address": "123 Business Ave, Suite 100",
  "business.town": "San Francisco",
  "business.state": "CA",
  "business.zip": "94107",
  "business.email": "contact@acmecorp.com",
  "business.phone": "(555) 123-4567",
  "business.einVatId": "",
  "customer.businessPersonalName": "TechNova Solutions",
  "customer.officerPersonalName": "Alex Johnson",
  "customer.address": "456 Innovation Way",
  "customer.town": "Palo Alto",
  "customer.state": "CA",
  "customer.zip": "94301",
  "customer.shippingAddress": "123 Business Ave, Suite 100",
  "customer.shippingTown": "San Francisco",
  "customer.shippingState": "CA",
  "customer.shippingZip": "94107",
  "customer.email": "alex@technovasolutions.com",
  "customer.account": "TN-2023-01",
  "items": [
    {
      "name": "Website Design",
      "description": "Custom responsive website design including user experience research and mockups",
      "quantity": 2,
      "pricePerItem": 1000.0,
      "discount": 10
    },
    {
      "name": "Website Backend",
      "description": "Custom responsive website design including user experience research and mockups",
      "quantity": 1,
      "pricePerItem": 100.0,
      "discount": 5
    },
    {
      "name": "Website Backend",
      "description": "Custom responsive website design including user experience research and mockups",
      "quantity": 1,
      "pricePerItem": 100.0,
      "discount": 5
    },
    {
      "name": "Website Backend",
      "description": "Custom responsive website design including user experience research and mockups",
      "quantity": 1,
      "pricePerItem": 100.0,
      "discount": 5
    }
  ],
  "invoice.date": "2025-07-02",
  "invoice.dueDate": "2025-08-02",
  "invoice.notes": "",
  "invoice.status": "Paid",
  "invoice.projectReference": "Website Redesign Q2 2025",
  "paymentTerms": "Net 30",
  "poNumber": "PO-TN-2025-123",
  "paymentMethods" : [{
    "type": ["Cash", "Zelle", "Crypto"],
    "description": "Office payment",
    "cashDeliveryAddress": "Lenina st., b 110",
    "cashDeliveryTown": "New York",
    "cashDeliveryState": "NY",
    "cashDeliveryZip": "123456",
    "bankName": "Bank Of America",
    "accountNumber": "123",
    "routingNumber": "321",
    "account": "9516516161566165",
    "cryptoName": "cryptoJD",
    "cryptoAddress": "crypto.cr",
    "paymentSite": "payme.com",
    "methodName": "methodName",
    "methodDescription": "methodDescription"
  }]

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

#### Get invoice

```http
  GET /invoice/${id}
```

| Parameter | Type     | Description                        |
| :-------- | :------- |:-----------------------------------|
| `id`      | `string` | **Required**. Invoice ID xxxx-xxxx |

Возвращает json
```json
{
    "id": "683cd9df5065a",
    "url": "http://host1880171.hostland.pro/generated/683cd9df5065a.pdf"
}
```
| Parameter  | Type     | Description    |
|:-----------| :------- |:---------------|
| `id`       | `string` | Invoice ID     |
| `file_url` | `string` | ссылка на файл |
| `data`     | `string` | Данные         |