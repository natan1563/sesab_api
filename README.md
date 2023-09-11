Este guia fornece instruções passo a passo para configurar o projeto Laravel "sesab_api" em sua máquina local. Certifique-se de seguir todas as etapas cuidadosamente para garantir uma configuração bem-sucedida.

Pré-requisitos
Antes de começar, você deve ter o seguinte software instalado em sua máquina:

PHP - Versão 7.4 ou superior.
Composer - Gerenciador de pacotes PHP.
Node.js - Versão 12 ou superior.
NPM - Gerenciador de pacotes JavaScript.
Redis - Deve estar instalado e em execução em sua máquina.
Passos de Configuração
Siga estas etapas para configurar o projeto "sesab_api" em sua máquina:

1. Clone o repositório
Abra o terminal e navegue até o diretório onde deseja clonar o projeto. Em seguida, execute o seguinte comando:

bash
Copy code
git clone git@github.com:natan1563/sesab_api.git
2. Instale as dependências do Composer
Navegue até o diretório do projeto "sesab_api" e execute o seguinte comando para instalar as dependências do Composer:

bash
Copy code
cd sesab_api
composer install
3. Crie um arquivo .env
Crie um arquivo .env na raiz do projeto usando o exemplo fornecido em .env.example. Você pode copiar o arquivo de exemplo e renomeá-lo:

bash
Copy code
cp .env.example .env
4. Gere uma chave de aplicativo
Execute o seguinte comando para gerar uma chave de aplicativo:

bash
Copy code
php artisan key:generate
5. Configure o banco de dados
Edite o arquivo .env e configure as informações do banco de dados de acordo com suas configurações locais:

plaintext
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sesab_api
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
Certifique-se de criar o banco de dados "sesab_api" no MySQL antes de continuar.

6. Configure o Redis
Certifique-se de que o Redis esteja instalado e em execução em sua máquina. O Laravel utiliza o Redis para várias funcionalidades, como filas de tarefas. Não é necessário configurar nada no arquivo .env para o Redis, pois as configurações padrão geralmente funcionam.

7. Execute as migrações
Execute o seguinte comando para executar as migrações do banco de dados:

bash
Copy code
php artisan migrate
8. Inicie o servidor Laravel
Para iniciar o servidor de desenvolvimento do Laravel, execute o seguinte comando:

bash
Copy code
php artisan serve
O servidor será iniciado em http://localhost:8000. Você pode acessar o aplicativo Laravel no seu navegador.

Conclusão
Agora, o projeto Laravel "sesab_api" deve estar configurado e em execução em sua máquina local. Você pode começar a desenvolver e testar seu projeto. Certifique-se de verificar a documentação oficial do Laravel para obter mais informações sobre como usar o framework: Documentação do Laravel.

Nota: Lembre-se de que este é um guia básico de configuração. Dependendo das configurações específicas do seu ambiente de desenvolvimento, você pode precisar ajustar algumas configurações ou instalar extensões adicionais para o PHP ou o Laravel.
