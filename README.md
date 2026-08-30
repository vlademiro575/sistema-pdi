# 🚀 Sistema PDI (Pesquisa, Desenvolvimento e Inovação)

**A plataforma segura e auditável para a gestão administrativa e financeira de projetos de pesquisa acadêmica.**

## 📖 O que é o Sistema PDI? 
O **Sistema PDI** é uma ferramenta criada para facilitar a vida de professores, pesquisadores, universidades e fundações de apoio (como FCPC, FASTEF, etc.). Ele acaba com a confusão das planilhas manuais e da papelada física, centralizando tudo em um só lugar. 
Com ele, você pode controlar o orçamento da pesquisa, o pagamento de bolsistas e as despesas diárias de forma automática, garantindo total transparência para órgãos de controle (como TCU e CGU). 

**A ideia é simples:** devolver o tempo do pesquisador para a ciência, tirando de suas costas a burocracia da prestação de contas.

## ✨ Características Principais
* **Transparência e Auditoria (Nada se perde):** O sistema possui uma "lixeira histórica" (Shadow Tables). Tudo o que é criado, alterado ou excluído é rastreado invisivelmente pelo banco de dados. Sabe-se exatamente *quem* fez e *quando*.
* **Automação de Saldos:** O saldo do projeto se atualiza sozinho. Assim que uma despesa é inserida, o sistema desconta o valor da categoria correta e gera um extrato, funcionando como a sua conta bancária.
* **Seguro e Descentralizado:** Desenvolvido com proteção de senhas modernas e perfis de acesso. Cada pessoa (Coordenador, Bolsista, Administrador) só enxerga o que tem permissão para ver.
* **Fácil Instalação:** Não exige servidores de banco de dados pesados, rodando de forma ágil e segura em qualquer ambiente através do SQLite. O sistema foi projetado para aceitar outros bancos mas, por enquanto, apenas o SQLite está sendo usado por questões de praticidade durante o desenvolvimento/testes.

## 🛠️ Funcionalidades
* 📁 **Gestão de Projetos:** Cadastro completo de projetos vinculando o professor responsável, a fundação financiadora e os prazos de vigência.
* 💰 **Controle Orçamentário (Rubricas):** Divisão do dinheiro do projeto em categorias (Ex: Custeio, Capital, Bolsas). Cada rubrica tem seu próprio saldo controlado rigorosamente.
* 👥 **Gestão de Recursos Humanos:** Cadastro de professores, coordenadores e bolsistas.
* 🧾 **Controle de Despesas e Extratos:** Registro de compras, inserção de notas fiscais, estornos e geração automática de extratos de movimentação.

[https://vlademiro575.github.io/sistema-pdi/](https://vlademiro575.github.io/sistema-pdi/)

---

## 💻 Passo a Passo de Instalação (Para Desenvolvedores)

O sistema foi desenvolvido utilizando **PHP 8+**, **CodeIgniter 4**, e **SQLite3**. A instalação a seguir é focada em ambientes Linux (Debian/Ubuntu), mas pode ser adaptada para Windows/Mac.

### 1. Pré-requisitos
No servidor ou máquina local, certifique-se de ter instalado o PHP 8.x e as extensões vitais para o funcionamento do CodeIgniter 4 e do SQLite3:
```bash
sudo apt update
sudo apt install -y php php-cli php-mbstring php-intl php-sqlite3 php-curl php-zip php-xml unzip git composer
```

### 2. Clonando o Projeto
Abra o terminal, escolha o diretório web e baixe o repositório oficial:

```bash
git clone https://github.com/vlademiro575/sistema-pdi.git
cd sistema-pdi
```

### 3. Instalando as Dependências

Execute o Composer para baixar todas as bibliotecas e pacotes necessários para o CodeIgniter 4 funcionar:

```bash
composer install
```

### 4. Atribuindo permissões de escrita na pasta `writable`

Atribua permissões de escrita na pasta `writable`. Essa pasta contém várias subpastas, onde ficam o banco de dados, sessões, logs, etc.

```bash
chmod -R 775 writable
```

### 5. Rodando o Servidor Localmente

Para levantar a aplicação utilizando o servidor embutido (Spark) do CodeIgniter:  

```bash
php spark serve --host 0.0.0.0 --port 8080
```

Pronto! Basta acessar http://localhost:8080 no seu navegador e você terá acesso total ao Sistema PDI.  

### 7. Primeiro login

* Usuário: admin
* Senha: 123456


