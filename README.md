# Mini CRM - Setup e Execução

Este projeto é otimizado para **Laravel + Docker + Node.js** e utiliza **queues** e **Reverb** para processamento de scores em tempo real.

Pré-requisitos

- Docker e Docker Compose instalados
- Node.js >= 18
- Composer
- NPM ou Yarn

---

## 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd mini-crm
```

## 2. Copie o arquivo de exemplo do .env

```bash
cp .env.example .env

```

## 3. Edite o .env com as suas configurações locais, incluindo

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mini_crm
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_DRIVER=file
QUEUE_CONNECTION=redis
REDIS_HOST=redis
SESSION_DRIVER=file

BROADCAST_DRIVER=reverb
REVERB_APP_KEY=...
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME=http
VITE_BACKEND_URL=http://localhost:8000

## 4. Levantar os contêineres Docker

```bash
docker compose up -d 
```

> Isso irá levantar os serviços: Laravel (PHP), MySQL, Redis, etc.


## 5. Instalar dependências Node.js

```bash
docker exec -it mini-crm-app npm install
```

## 6. Configurar o ambiente

```bash
cp .env.example .env
docker exec -it mini-crm-app php artisan key:generate
```

> Configure no `.env` os dados de conexão com MySQL, Redis e Reverb, se necessário.

## 7. Executar migrations e seeders

```bash
docker exec -it mini-crm-app php artisan migrate --seed
```

> Isso irá criar as tabelas no banco e popular dados iniciais, como contatos de teste.

## 8. Instalar dependências JS

```bash
docker exec -it mini-crm-app npm install
docker exec -it mini-crm-app npm run dev

```

## 9. Levantar o worker de queues

```bash
docker exec -it mini-crm-app php artisan queue:work --queue=contacts
```

> O worker processa os jobs de score dos contatos.

## 10. Iniciar Reverb (WebSocket)

```bash
docker exec -it mini-crm-app php artisan reverb:start
```

> Reverb é usado para notificar o frontend em tempo real quando o score de um contato é processado.

## 11. Rodar o servidor Laravel

```bash
docker exec -it mini-crm-app php artisan serve --host=0.0.0.0 --port=8000
```

## 12. Rodar o front-end (Vite)

```bash
docker exec -it mini-crm-app npm run dev
```

> Isso compila os arquivos JS/CSS e permite usar o frontend com Hot Reload.

---

## 13. Testando os endpoints (Postman)

POST http://localhost:8000/api/contacts/{id}/process-score

Resposta esperada:
{
    "status": "success",
    "message": "Score processing has begun.",
    "data": null
}

CRUD de contatos
Endpoints principales

GET /api/contacts → Listado de contactos

GET /api/contacts/{id} 

POST /api/contacts 

PUT /api/contacts/{id} 

DELETE /api/contacts/{id} (softdelete)

POST /api/contacts/{id}/process-score


Todos os endpoints retornam respostas em formato JSON, utilizando recursos da API para garantir consistência.
---

## 14. Exemplo de escuta de canal em JS

No frontend, você pode escutar os updates de um contato assim:

```js
const contactId = 1; // Id do contato que deseja escutar
const channel = window.reverb.channel(`contacts.${contactId}`);

channel.listen((payload) => {
    console.log("Contato atualizado:", payload);
});
```

> Esse código só funciona se o Reverb estiver rodando e o frontend conectado ao WebSocket.

---

## 15. Executando testes

```bash
docker exec -it mini-crm-app php artisan test
```

Cobre:

CRUD de contatos (com soft delete)

Form Requests e API Resources

Observer (saving e created)

Dispatcher do job de processamento de score

Atualização de score no banco

Logging no arquivo contacts.log

---

## Observações

* Certifique-se de que **worker** e **Reverb** estão sempre ativos para processar scores e enviar notificações.
* Logs de criação e updates de contatos são gravados em `storage/logs/contacts.log`.
* O endpoint `/api/contacts/{id}/process-score` processa o score de um contato específico.

