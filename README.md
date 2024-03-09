# Teste Prático - Seleção Desenvolvimento - 2024 - Projeto 2
Criação de dois sistemas isolados (dois projetos separados), um fornecedor de dados e outro consumidor.

#### Projeto 1:

Criar um projeto para consumir e armazenar bem como disponibilizar via API autenticada via token
as informações retornadas da API coinmarketcap: [https://coinmarketcap.com/api/documentation/v1/](https://coinmarketcap.com/api/documentation/v1/)

- Ações a consumirem e armazenar localmente
    
    - https://coinmarketcap.com/api/documentation/v1/#operation/getV1CryptocurrencyListingsHistorical
        
        - Informações de cada moeda de maneira relacional (Mysql/PostgreSQL)
            
            - Nome
            - Market Cap
            - Preço U$
            - Volume 24 horas
            - Variação 24 horas.
            
- Criar um endpoint para listar todas as informações, agrupada por nome da moeda, com filtro de ordenação por: name, price, ranking. 
- Criar CRUD que permite criar um grupo de moedas, por exemplo:
    - Grupo 1
        - BTC, BNB, ETH
    - Grupo 2
        - SOL, XRP, ADA


#### Projeto 2:
- Ter uma página, que vai ler a API do projeto 1, e listar de forma organizada apenas os nomes das moedas.
- Ao clicar em um nome, surgirá um modal que busca por ajax o restante das informações (nome, ranking, preço etc).
- Criar CRUD para os grupos. Ao clicar no grupo abrir um modal para exibir as moedas relacionadas ao grupo. Permite remover uma moeda específica do grupo se necessário.


## Instalação

Clone do projeto:

```sh
$ git clone git@github.com:juniorari/cplug.git
```

Acessar a pasta do projeto:

```sh
$ cd cplug
```

Criar e subir os containers:

```sh
$ docker-compose up --build -d
```

Baixar as dependências do composer
```sh
$ docker exec -it cplug_app_2 composer install -vvv
```

Copiar o arquivo `.env`

```sh
$ cp .env.example .env
```


O projeto está rodando em [http://localhost:8081/](http://localhost:8081/).

 

