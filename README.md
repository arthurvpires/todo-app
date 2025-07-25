# TodoApp

Sistema web completo para gerenciamento de tarefas, com autenticação, permissões e notificações. Desenvolvido com Laravel (API) e Vue.js (frontend), rodando em containers Docker para facilitar o ambiente de desenvolvimento.

## Tecnologias utilizadas

- **Laravel (PHP)** 
- **Vue.js (TypeScript)** 
- **Docker**
- **MySQL**

## Estrutura do projeto

- `/backend` – Contém a API RESTful desenvolvida em Laravel
- `/frontend` – Contém a aplicação frontend em Vue.js

## URLs de acesso

- Aplicação Vue.js: [http://localhost:5173](http://localhost:5173)
- API Laravel: [http://todo-app.local](http://todo-app.local)

## Funcionalidades

- Cadastro de usuários com autenticação protegida por Sanctum
- Cadastro de tarefas informando título, descrição, status e data de vencimento
- Permissões: apenas administradores podem criar tarefas
- Envio de e-mails assíncronos usando filas
- Testes unitários
- Busca de tarefas por palavra chave
- Exportar tarefas (CSV)
- Tela para administradores (listar, criar e apagar usuários)
- Validações com FormRequests
- Command Laravel que manipula dados (apaga tarefas completadas a mais de um ano)
- TaskObserver que observa se já existe uma tarefa criada com mesmo nome e descrição.

## Pré-requisitos

- Docker e Docker Compose instalados
- Node.js

## Como rodar o projeto

Clone este repositório:

```bash
git clone https://github.com/arthurvpires/todo-app.git
cd todo-app
```
# Configurando o Backend (Laravel + Docker)

1. Inicie os containers da API Laravel:

```bash
cd backend
docker-compose up -d --build
```

2. Clone o .env.example:
```
cp .env.example .env
```

3. Instale as dependências e popule o banco de dados:
 
```bash
docker exec -it todo-app-backend composer install
docker exec -it todo-app-backend php artisan key:generate
docker exec -it todo-app-backend php artisan migrate --seed
```
## Envio de emails (Opcional)
  - Usei o **Mailtrap** para fazer o teste de envio dos emails.
  - Crie uma conta em https://mailtrap.io e configure no **.env** para visualizar os emails recebidos. 
    
    ```
    MAIL_USERNAME=seu_username
    MAIL_PASSWORD=sua_senha
    ```
 - Os e-mails são enviados de forma assíncrona usando filas. Para que eles sejam processados corretamente, é necessário iniciar o worker com o comando abaixo:

   ```
   docker exec -it todo-app php artisan queue:work
   ````
## Comandos Laravel

  - O comando abaixo remove tarefas com status 'completed' que foram concluídas há um ano ou mais:
   ```
   docker exec -it todo-app php artisan delete-old-completed-tasks
   ````
  - Este comando envia e-mails para usuários que possuem tarefas com vencimento agendado para amanhã (necessário configurar o envio de emails no tópico anterior):
   ```
   docker exec -it todo-app php artisan email:send-to-task-due-tomorrow
   ````
 
## Configuração do Hosts:

  ### Windows
  1. Abra o Bloco de Notas como administrador
  2. Abra o arquivo: `C:\Windows\System32\drivers\etc\hosts`
  3. Adicione a seguinte linha:
  ```
  127.0.0.1 todo-app.local
  ```
  
  ### Linux/Mac
  1. Abra o terminal
  2. Execute o comando:
  ```bash
  sudo nano /etc/hosts
  ```
  3. Adicione a seguinte linha:
  ```bash
  127.0.0.1 todo-app.local
  ```

##  Testes

Para executar os testes criados:
  
```
docker exec -it todo-app php artisan test 
```

# Agora configurando o frontend:

```bash
cd ..
cd frontend
```

```bash
npm install
npm run dev
```

## Usuário de testes para API:
    - Email: admin@example.com
    - Senha: senha123

## Rotas disponíveis no frontend:

- URL base: `http://localhost:5173`

  - `/login`
    -  Login do usuário
  - `/dashboard`
     -  Tela principal com os endpoints da API 

   
   
