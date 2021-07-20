## Instruções do projeto
    - Fazer o clone do projeto

## Instruções para clonagem desse repositório 
	- clonar o repositório em um local que rode PHP ^7.3|^8.0
	- No terminal dentro da pasta do projeto rodar o comando : composer install
	- No arquivo .env você  configura o banco de dados nas linhas 29,30 e 31 tem alguns exemplos de conexão.
	- Para criar o banco de dados entrar na pasta do projeto pelo terminal/cmd e rodar o comando "php bin/console doctrine:database:create"
	- após a criação de banco de dados rodar o  comando "php bin/console doctrine:migrations:migrate" para criar as tabelas.