# 🚀 Instruções Rápidas de Uso

## Inicialização Rápida

1. **Clone o projeto**
   ```bash
   git clone <url-do-repositorio>
   cd biblioteca-laravel
   ```

2. **Inicialização (Escolha uma opção)**

   ### Opção A: Usando o script `start.sh` (Linux/macOS/Git Bash/WSL)
   ```bash
   ./start.sh
   ```

   ### Opção B: Manualmente no Windows (PowerShell/CMD)
   ```powershell
   # Navegue até o diretório do projeto
   cd biblioteca-laravel

   # Copiar arquivo de ambiente (se não existir)
   Copy-Item .env.example .env -ErrorAction SilentlyContinue

   # Construir containers
   docker-compose build

   # Iniciar containers
   docker-compose up -d

   # Aguardar MySQL estar pronto (30 segundos)
   Start-Sleep -Seconds 30

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

3. **Aguarde a inicialização** (aproximadamente 2-3 minutos)

4. **Acesse a aplicação**
   - API: http://localhost:8000/api
   - phpMyAdmin: http://localhost:8080

## Testando a API

### 1. Verificar Status
```bash
curl http://localhost:8000/api/
```

### 2. Criar um Usuário
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"nome": "João Silva", "email": "joao@email.com", "numero_cadastro": "USR001"}'
```

### 3. Listar Usuários
```bash
curl http://localhost:8000/api/users
```

## Importar no Insomnia

1. Abra o Insomnia
2. Clique em "Import/Export"
3. Selecione "Import Data"
4. Escolha o arquivo `insomnia-collection.json`
5. Todas as requisições estarão disponíveis organizadas por categoria

## Comandos Úteis

```bash
# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Executar comando Laravel
docker-compose exec app php artisan [comando]

# Acessar MySQL
docker-compose exec mysql mysql -u biblioteca_user -p
```

## Estrutura da API

- **GET /api/users** - Listar usuários
- **POST /api/users** - Criar usuário
- **GET /api/generos** - Listar gêneros
- **POST /api/generos** - Criar gênero
- **GET /api/livros** - Listar livros
- **POST /api/livros** - Criar livro
- **GET /api/emprestimos** - Listar empréstimos
- **POST /api/emprestimos** - Criar empréstimo
- **PATCH /api/emprestimos/{id}/devolver** - Devolver livro

Para documentação completa, consulte o README.md

