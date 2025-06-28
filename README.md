# Sistema de Gerenciamento de Biblioteca - API Laravel

## 📋 Descrição

Este projeto é um sistema completo de gerenciamento de biblioteca desenvolvido em Laravel/PHP com Docker, criado para atender aos requisitos de um teste técnico para desenvolvedor back-end. O sistema permite o gerenciamento eficiente de usuários, livros, gêneros literários e empréstimos, oferecendo controle total sobre a disponibilidade dos livros e prazos de devolução.

## 🎯 Objetivos do Sistema

O sistema foi desenvolvido para resolver os seguintes problemas de uma biblioteca:

- **Gerenciamento manual ineficiente**: Substituir o controle manual por um sistema automatizado
- **Controle de empréstimos**: Facilitar o acompanhamento de empréstimos ativos, devoluções e atrasos
- **Organização por gêneros**: Permitir categorização e busca de livros por gênero literário
- **Disponibilidade em tempo real**: Identificar instantaneamente quais livros estão disponíveis ou emprestados

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 10.x**: Framework PHP moderno e robusto
- **PHP 8.2**: Linguagem de programação principal
- **MySQL 8.0**: Sistema de gerenciamento de banco de dados

### Infraestrutura
- **Docker & Docker Compose**: Containerização e orquestração
- **Nginx**: Servidor web de alta performance
- **phpMyAdmin**: Interface web para gerenciamento do MySQL

### Ferramentas de Desenvolvimento
- **Composer**: Gerenciador de dependências PHP
- **Artisan**: CLI do Laravel para comandos e migrações

## 📁 Estrutura do Projeto

```
biblioteca-laravel/
├── app/
│   ├── Http/Controllers/     # Controllers da API
│   └── Models/              # Modelos Eloquent
├── database/
│   ├── migrations/          # Migrações do banco de dados
│   └── seeders/            # Seeders para dados iniciais
├── routes/
│   ├── api.php             # Rotas da API
│   └── web.php             # Rotas web
├── docker/                 # Configurações Docker
│   ├── nginx/              # Configuração Nginx
│   ├── php/                # Configuração PHP
│   └── mysql/              # Scripts MySQL
├── public/                 # Arquivos públicos
├── config/                 # Configurações Laravel
├── docker-compose.yml      # Orquestração dos containers
├── Dockerfile             # Imagem Docker da aplicação
└── start.sh               # Script de inicialização
```

## 🗄️ Modelagem do Banco de Dados

### Tabelas Principais

#### 1. **users** - Usuários da Biblioteca
- `id`: Chave primária
- `nome`: Nome completo do usuário
- `email`: Email único do usuário
- `numero_cadastro`: Número único de cadastro
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

#### 2. **generos** - Gêneros Literários
- `id`: Chave primária
- `nome`: Nome do gênero (único)
- `descricao`: Descrição opcional do gênero
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

#### 3. **livros** - Acervo de Livros
- `id`: Chave primária
- `nome`: Título do livro
- `autor`: Nome do autor
- `numero_registro`: Número único de registro
- `situacao`: Status (disponível/emprestado)
- `genero_id`: Chave estrangeira para gêneros
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

#### 4. **emprestimos** - Controle de Empréstimos
- `id`: Chave primária
- `user_id`: Chave estrangeira para usuários
- `livro_id`: Chave estrangeira para livros
- `data_emprestimo`: Data do empréstimo
- `data_devolucao_prevista`: Data prevista para devolução
- `data_devolucao_real`: Data real da devolução
- `status`: Status (ativo/devolvido/atrasado)
- `observacoes`: Observações opcionais
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

### Relacionamentos

- **User** → **Emprestimo**: Um usuário pode ter vários empréstimos (1:N)
- **Livro** → **Emprestimo**: Um livro pode ter vários empréstimos (1:N)
- **Genero** → **Livro**: Um gênero pode ter vários livros (1:N)

## 🔧 Instalação e Configuração

### Pré-requisitos

- Docker Desktop instalado
- Docker Compose instalado
- Git instalado
- Porta 8080 disponível (aplicação)
- Porta 8080 disponível (phpMyAdmin)
- Porta 3306 disponível (MySQL)

### Passo a Passo

#### 1. Clone o Repositório
```bash
git clone https://github.com/Northon-gracik/biblioteca-laravel.git
cd biblioteca-laravel
```

#### 2. Inicialização Automática
```bash
# Executar script de inicialização (recomendado)
./start.sh #(WSL ou Linux)
./start.ps1 #(Windows)
```

#### 3. Inicialização Manual (alternativa)
```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Construir containers
docker-compose build

# Iniciar containers
docker-compose up -d

# Aguardar MySQL estar pronto (30 segundos)
sleep 30

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrações
docker-compose exec app php artisan migrate --force

# Executar seeders
docker-compose exec app php artisan db:seed --force

# Limpar cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

### Verificação da Instalação

Após a instalação, verifique se os serviços estão funcionando:

- **Aplicação**: http://localhost:8080
- **API**: http://localhost:8080/api
- **phpMyAdmin**: http://localhost:8081
- **Status da API**: http://localhost:8080/api/info

## 📡 Documentação da API

### Base URL
```
http://localhost:8080/api
```

### Headers Necessários
```
Content-Type: application/json
Accept: application/json
```



### 👥 Endpoints - Usuários

#### Listar Todos os Usuários
```http
GET /api/users
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nome": "João Silva",
            "email": "joao@email.com",
            "numero_cadastro": "USR001",
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T10:00:00.000000Z",
            "emprestimos_ativos": []
        }
    ],
    "message": "Usuários listados com sucesso"
}
```

#### Criar Novo Usuário
```http
POST /api/users
```

**Corpo da Requisição:**
```json
{
    "nome": "Maria Santos",
    "email": "maria@email.com",
    "numero_cadastro": "USR002"
}
```

**Resposta de Sucesso (201):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "nome": "Maria Santos",
        "email": "maria@email.com",
        "numero_cadastro": "USR002",
        "created_at": "2024-01-01T10:00:00.000000Z",
        "updated_at": "2024-01-01T10:00:00.000000Z"
    },
    "message": "Usuário criado com sucesso"
}
```

#### Buscar Usuário por ID
```http
GET /api/users/{id}
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@email.com",
        "numero_cadastro": "USR001",
        "emprestimos": [],
        "emprestimos_ativos": []
    },
    "message": "Usuário encontrado"
}
```

#### Atualizar Usuário
```http
PUT /api/users/{id}
```

**Corpo da Requisição:**
```json
{
    "nome": "João Silva Santos",
    "email": "joao.santos@email.com",
    "numero_cadastro": "USR001"
}
```

#### Excluir Usuário
```http
DELETE /api/users/{id}
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "message": "Usuário excluído com sucesso"
}
```

### 📚 Endpoints - Gêneros

#### Listar Todos os Gêneros
```http
GET /api/generos
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nome": "Ficção",
            "descricao": "Livros de ficção literária e narrativas imaginárias",
            "livros_count": 5,
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T10:00:00.000000Z"
        }
    ],
    "message": "Gêneros listados com sucesso"
}
```

#### Criar Novo Gênero
```http
POST /api/generos
```

**Corpo da Requisição:**
```json
{
    "nome": "Terror",
    "descricao": "Livros de terror e suspense psicológico"
}
```

#### Buscar Gênero por ID
```http
GET /api/generos/{id}
```

#### Atualizar Gênero
```http
PUT /api/generos/{id}
```

#### Excluir Gênero
```http
DELETE /api/generos/{id}
```

### 📖 Endpoints - Livros

#### Listar Todos os Livros
```http
GET /api/livros
```

**Parâmetros de Query Opcionais:**
- `genero_id`: Filtrar por gênero
- `situacao`: Filtrar por situação (disponivel/emprestado)
- `autor`: Buscar por autor (busca parcial)

**Exemplo:**
```http
GET /api/livros?genero_id=1&situacao=disponivel
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nome": "Dom Casmurro",
            "autor": "Machado de Assis",
            "numero_registro": "LIV001",
            "situacao": "disponivel",
            "genero_id": 1,
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T10:00:00.000000Z",
            "genero": {
                "id": 1,
                "nome": "Ficção",
                "descricao": "Livros de ficção literária"
            },
            "emprestimo_ativo": null
        }
    ],
    "message": "Livros listados com sucesso"
}
```

#### Listar Apenas Livros Disponíveis
```http
GET /api/livros-disponiveis
```

#### Criar Novo Livro
```http
POST /api/livros
```

**Corpo da Requisição:**
```json
{
    "nome": "O Cortiço",
    "autor": "Aluísio Azevedo",
    "numero_registro": "LIV002",
    "situacao": "disponivel",
    "genero_id": 1
}
```

#### Buscar Livro por ID
```http
GET /api/livros/{id}
```

#### Atualizar Livro
```http
PUT /api/livros/{id}
```

#### Excluir Livro
```http
DELETE /api/livros/{id}
```

### 📋 Endpoints - Empréstimos

#### Listar Todos os Empréstimos
```http
GET /api/emprestimos
```

**Parâmetros de Query Opcionais:**
- `status`: Filtrar por status (ativo/devolvido/atrasado/atrasados)
- `user_id`: Filtrar por usuário

**Exemplo:**
```http
GET /api/emprestimos?status=ativo&user_id=1
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "livro_id": 1,
            "data_emprestimo": "2024-01-01",
            "data_devolucao_prevista": "2024-01-15",
            "data_devolucao_real": null,
            "status": "ativo",
            "observacoes": null,
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T10:00:00.000000Z",
            "user": {
                "id": 1,
                "nome": "João Silva",
                "email": "joao@email.com"
            },
            "livro": {
                "id": 1,
                "nome": "Dom Casmurro",
                "autor": "Machado de Assis",
                "genero": {
                    "id": 1,
                    "nome": "Ficção"
                }
            }
        }
    ],
    "message": "Empréstimos listados com sucesso"
}
```

#### Listar Empréstimos Atrasados
```http
GET /api/emprestimos-atrasados
```

#### Criar Novo Empréstimo
```http
POST /api/emprestimos
```

**Corpo da Requisição:**
```json
{
    "user_id": 1,
    "livro_id": 2,
    "data_emprestimo": "2024-01-01",
    "data_devolucao_prevista": "2024-01-15",
    "observacoes": "Empréstimo regular"
}
```

**Resposta de Sucesso (201):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "user_id": 1,
        "livro_id": 2,
        "data_emprestimo": "2024-01-01",
        "data_devolucao_prevista": "2024-01-15",
        "status": "ativo",
        "user": {...},
        "livro": {...}
    },
    "message": "Empréstimo criado com sucesso"
}
```

#### Buscar Empréstimo por ID
```http
GET /api/emprestimos/{id}
```

#### Atualizar Empréstimo
```http
PUT /api/emprestimos/{id}
```

#### Devolver Livro
```http
PATCH /api/emprestimos/{id}/devolver
```

**Resposta de Sucesso (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "status": "devolvido",
        "data_devolucao_real": "2024-01-10T14:30:00.000000Z",
        "user": {...},
        "livro": {...}
    },
    "message": "Livro devolvido com sucesso"
}
```

#### Marcar Empréstimo como Atrasado
```http
PATCH /api/emprestimos/{id}/marcar-atrasado
```

#### Excluir Empréstimo
```http
DELETE /api/emprestimos/{id}
```

### 📊 Endpoints Informativos

#### Informações da API
```http
GET /api/info
```

**Resposta:**
```json
{
    "api": "Sistema de Biblioteca",
    "version": "1.0.0",
    "description": "API para gerenciamento de biblioteca com usuários, livros e empréstimos",
    "endpoints": {
        "users": "Gerenciamento de usuários",
        "generos": "Gerenciamento de gêneros de livros",
        "livros": "Gerenciamento de livros",
        "emprestimos": "Gerenciamento de empréstimos"
    }
}
```

#### Status da API
```http
GET /api/
```

**Resposta:**
```json
{
    "message": "API Sistema de Biblioteca funcionando!",
    "version": "1.0.0",
    "timestamp": "2024-01-01T10:00:00.000000Z"
}
```

## ⚠️ Tratamento de Erros

### Códigos de Status HTTP

- **200**: Sucesso
- **201**: Criado com sucesso
- **400**: Erro de validação ou regra de negócio
- **404**: Recurso não encontrado
- **422**: Dados inválidos
- **500**: Erro interno do servidor

### Formato de Resposta de Erro

```json
{
    "success": false,
    "message": "Descrição do erro",
    "errors": {
        "campo": ["Mensagem de erro específica"]
    }
}
```

### Exemplos de Erros Comuns

#### Validação de Dados
```json
{
    "success": false,
    "message": "Dados inválidos",
    "errors": {
        "email": ["O campo email deve ser um endereço de email válido"],
        "numero_cadastro": ["O campo numero_cadastro já está sendo utilizado"]
    }
}
```

#### Regra de Negócio
```json
{
    "success": false,
    "message": "Livro não está disponível para empréstimo"
}
```

#### Recurso Não Encontrado
```json
{
    "success": false,
    "message": "Usuário não encontrado"
}
```

## 🔒 Regras de Negócio

### Usuários
- Email deve ser único no sistema
- Número de cadastro deve ser único no sistema
- Não é possível excluir usuário com empréstimos ativos
- Todos os campos são obrigatórios

### Gêneros
- Nome do gênero deve ser único
- Não é possível excluir gênero que possui livros cadastrados
- Descrição é opcional

### Livros
- Número de registro deve ser único
- Situação pode ser apenas "disponivel" ou "emprestado"
- Deve estar associado a um gênero válido
- Não é possível excluir livro que está emprestado

### Empréstimos
- Livro deve estar disponível para criar empréstimo
- Data de devolução prevista deve ser posterior à data de empréstimo
- Ao criar empréstimo, livro automaticamente fica "emprestado"
- Ao devolver, livro automaticamente fica "disponivel"
- Status pode ser "ativo", "devolvido" ou "atrasado"
- Empréstimos são considerados atrasados quando a data prevista passou e não foram devolvidos

## 🛠️ Comandos Úteis

### Gerenciamento de Containers
```bash
# Iniciar todos os serviços
docker-compose up -d

# Parar todos os serviços
docker-compose down

# Ver logs dos containers
docker-compose logs -f

# Reconstruir containers
docker-compose build --no-cache

# Executar comando no container da aplicação
docker-compose exec app [comando]
```

### Comandos Laravel
```bash
# Executar migrações
docker-compose exec app php artisan migrate

# Executar seeders
docker-compose exec app php artisan db:seed

# Limpar cache
docker-compose exec app php artisan cache:clear

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Listar rotas
docker-compose exec app php artisan route:list
```

### Acesso ao Banco de Dados
```bash
# Conectar ao MySQL via linha de comando
docker-compose exec mysql mysql -u biblioteca_user -p biblioteca

# Backup do banco de dados
docker-compose exec mysql mysqldump -u biblioteca_user -p biblioteca > backup.sql

# Restaurar backup
docker-compose exec -i mysql mysql -u biblioteca_user -p biblioteca < backup.sql
```

## 🧪 Testes e Validação

### Testando a API

#### 1. Verificar Status da API
```bash
curl http://localhost:8080/api/
```

#### 2. Criar um Usuário
```bash
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Teste Usuario",
    "email": "teste@email.com",
    "numero_cadastro": "TEST001"
  }'
```

#### 3. Listar Usuários
```bash
curl http://localhost:8080/api/users
```

#### 4. Criar um Gênero
```bash
curl -X POST http://localhost:8080/api/generos \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Teste Genero",
    "descricao": "Gênero para testes"
  }'
```

#### 5. Criar um Livro
```bash
curl -X POST http://localhost:8080/api/livros \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Livro de Teste",
    "autor": "Autor Teste",
    "numero_registro": "TEST001",
    "situacao": "disponivel",
    "genero_id": 1
  }'
```

#### 6. Criar um Empréstimo
```bash
curl -X POST http://localhost:8080/api/emprestimos \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "livro_id": 1,
    "data_emprestimo": "2024-01-01",
    "data_devolucao_prevista": "2024-01-15"
  }'
```

### Cenários de Teste

#### Fluxo Completo de Empréstimo
1. Criar usuário
2. Criar gênero
3. Criar livro
4. Verificar que livro está disponível
5. Criar empréstimo
6. Verificar que livro ficou emprestado
7. Devolver livro
8. Verificar que livro voltou a ficar disponível

#### Validação de Regras de Negócio
1. Tentar criar empréstimo com livro já emprestado
2. Tentar excluir usuário com empréstimo ativo
3. Tentar excluir livro emprestado
4. Tentar criar usuário com email duplicado

## 🚨 Solução de Problemas

### Problemas Comuns

#### Container não inicia
```bash
# Verificar logs
docker-compose logs app

# Reconstruir container
docker-compose build --no-cache app
docker-compose up -d
```

#### Erro de conexão com MySQL
```bash
# Verificar se MySQL está rodando
docker-compose ps

# Aguardar MySQL estar pronto
sleep 30

# Testar conexão
docker-compose exec mysql mysql -u biblioteca_user -p
```

#### Erro de permissão
```bash
# Ajustar permissões
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

#### Porta já em uso
```bash
# Verificar processos usando a porta
sudo lsof -i :8080

# Alterar porta no docker-compose.yml
# Trocar "8080:80" por "8001:80"
```

### Logs e Debugging

#### Verificar logs da aplicação
```bash
docker-compose logs -f app
```

#### Verificar logs do Nginx
```bash
docker-compose logs -f nginx
```

#### Verificar logs do MySQL
```bash
docker-compose logs -f mysql
```

#### Acessar container para debug
```bash
docker-compose exec app bash
```

## 🔧 Configurações Avançadas

### Variáveis de Ambiente

O arquivo `.env` contém todas as configurações importantes:

```env
# Aplicação
APP_NAME="Sistema Biblioteca"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=biblioteca_user
DB_PASSWORD=biblioteca_password
```

### Personalização do Docker

#### Alterar versão do PHP
No `Dockerfile`, altere:
```dockerfile
FROM php:8.2-fpm
```

#### Adicionar extensões PHP
No `Dockerfile`, adicione:
```dockerfile
RUN docker-php-ext-install nova_extensao
```

#### Configurar Nginx
Edite o arquivo `docker/nginx/default.conf` para personalizar o servidor web.

### Performance

#### Otimização do Laravel
```bash
# Cache de configuração
docker-compose exec app php artisan config:cache

# Cache de rotas
docker-compose exec app php artisan route:cache

# Cache de views
docker-compose exec app php artisan view:cache

# Otimizar autoload
docker-compose exec app composer dump-autoload --optimize
```

#### Otimização do MySQL
Edite `docker-compose.yml` para adicionar configurações MySQL:
```yaml
mysql:
  command: --innodb-buffer-pool-size=256M --max-connections=200
```

## 📈 Monitoramento

### Métricas da Aplicação

#### Status dos Containers
```bash
docker-compose ps
```

#### Uso de Recursos
```bash
docker stats
```

#### Logs em Tempo Real
```bash
docker-compose logs -f --tail=100
```

### Backup e Recuperação

#### Backup Automático do Banco
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec mysql mysqldump -u biblioteca_user -p biblioteca > backup_$DATE.sql
```

#### Restaurar Backup
```bash
docker-compose exec -i mysql mysql -u biblioteca_user -p biblioteca < backup_20240101_120000.sql
```
