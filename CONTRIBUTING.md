# Contribuindo

Então você quer contribuir com este projeto? Muito obrigado pelo seu interesse! Aqui estão descritos alguns padrões que são utilizados durante o meu desenvolvimento e que eu gostaria que você também seguisse. Então, vamos lá!

## Abrindo uma Issue

Possui algum problema? Você não precisa saber programação para ajudar neste projeto.

Abra uma [nova _issue_](//github.com/wandersonwhcr/balance/issues/new) e descreva os problemas encontrados da melhor maneira possível. Informe o passo-a-passo para gerar o problema, incluindo botões pressionados, _screenshots_ de telas ou até _dump_ de banco de dados.

Lembre-se de que a informação sobre o problema é um item precioso para solucioná-lo.

## Estrutura do Repositório

Este projeto é controlado com _tags_ no formato de versionamento semântico e 2 _branches_ principais: `master` e `develop`. Todas as _tags_ representam ambientes estáveis e a última _tag_ sempre aponta para o _branch_ `master`. Todos os _branches_ saem do _branch_ `master` e retornam para o _branch_ `develop`. Quando uma _tag_ é finalizada, todos os _branches_ que não retornaram deverão sofrer um _rebase_ para o _branch_ `master`.

Certo, mas o que isso quer dizer?

Eu tento sempre manter a melhor árvore Git possível neste projeto. Você pode visualizá-la acessando a [linha de desenvolvimento](//github.com/wandersonwhcr/balance/network) do projeto. Conforme visualização, todos os _branches_ saem da última _tag_ e retornam antes da próxima _tag_. Assim, eu consigo ter uma linha de desenvolvimento concisa e de ótima visualização em _softwares_ como o Gitk.

Lembre-se dessa estrutura! Ela é muito importante!

## Nomenclatura de Referências

Somente 2 _branches_ possuem nomes fora do padrão: o `master` e o `develop`. Todos os outros _branches_ devem possuir o formato `/^issue-[1-9][0-9]*$/`, representando o número da _issue_ que gerou aquele _branch_. Portanto, todos os _branches_ criados devem ser resultantes da abertura de uma _issue_ no projeto. Descrevendo melhor, todos os _branches_ devem iniciar com `issue-` e finalizar com o número da _issue_ que gerou aquele _branch_.

Por exemplo, se existe uma _issue_ 42 que irá gerar uma alteração de código, o _branch_ que será criado terá o nome `issue-42`.

Como eu não utilizo um _fork_ do projeto e efetuo _commits_ diretamente, em alguns momentos você poderá encontrar _branches_ neste padrão. Ignore, são os meus _branches_. Não utilize-os, eles são extremamente instáveis e podem causar uma fissão nuclear.

As _tags_ obedecem ao [versionamento semântico](http://semver.org/) comum aos projetos _opensource_ disponíveis. A última _tag_ sempre será o projeto mais estável e todas elas apontarão para o _branch_ `master`.

## Ambiente de Desenvolvimento

Oba! Você quer contribuir? Então primeiramente, faça um _fork_ deste projeto utilizando o próprio Github e faça um _clone_ em seu ambiente de trabalho. Para desenvolvimento, eu utilizo uma máquina Vagrant neste projeto. Para inicializá-la, basta copiar o arquivo de configurações do Vagrant que está na distribuição e solicitar a sua criação.

```bash
cp Vagrantfile.dist Vagrantfile
vagrant up
```

Todo o projeto será inicializado e estará pronto para execução, acessível no endereço `http://localhost:8000`. Também será instalado o PHPPgAdmin, disponível no endereço `http://localhost:9000`.

Caso o Vagrant encontrar estas portas ocupadas, ele irá informá-lo quais são as novas portas definidas.

## Patch Requests (PR)

YEAH! Patch Request! Vamos lá!

Seguindo os padrões de estrutura do projeto, sempre faça um _branch_ a partir do _branch_ `master`, nomeado no padrão `/^issue-[1-9][0-9]*$/`. Efetue seus _commits_, sempre com a mensagem possuindo o padrão `/^Issue #[1-9][0-9]*/`. Isto quer dizer que todos os seus _commits_ devem possuir mensagens que iniciam com um texto que aponta para a _issue_ relacionada ao _branch_. Isto faz com que o Github referencie os _commits_ com a _issue_, facilitando a navegação.

### Testes Unitários

Se você alterar o código-fonte do projeto, por favor, crie um teste unitário que faça **100%** de _coverage_ em suas alterações. Assim, vamos garantir que este projeto poderá rodar em futuras versões do PHP. Para executar os testes unitários no projeto, basta executar o [PHPUnit](https://phpunit.de/) disponível na instalação.

```bash
php vendor/bin/phpunit
```

Se sua alterações forem para novos recursos, os famosos _enhancements_, crie um teste unitário com o mesmo _namespace_ de suas novas classes. Por exemplo, se você criar uma classe nova chamada `Balance\Model\Persistence\FooBar`, crie um novo arquivo na estrutura de testes chamado `module/Balance/test/Balance/Model/Persistence/FooBarTest.php` com uma classe chamada `Balance\Model\Persistence\FooBarTest`.

Todavia, se você está corrigindo um _bug_ que está descrito na _issue_ 42, crie um arquivo na estrutura de testes chamado `module/Balance/test/Balance/Bugs/Issue42Test.php` com uma classe chamada `Balance\Bugs\Issue42Test`, que force a execução do _bug_ informado. Altere o código até que este _bug_ seja solucionado.

### Estrutura de Banco de Dados

O banco de dados possui uma estrutura gerenciada pelo [Phinx](https://phinx.org/) através de _migrations_. Para criar uma nova _migration_, utilize o seguinte comando.

```bash
php vendor/bin/phinx create AlterTableFooAddBar
```

Tente nomear a _migration_ conforme o padrão das criadas anteriormente. Particularmente, não possuo padrões para isto.

Lembre-se de sempre criar _migrations_ que consigam subir e descer versões corretamente, alterando e retornando alterações num formato que minimize a perda de informações em ambientes de produção.

### Travis CI

Depois que você enviar suas alterações para o Github, este projeto utiliza o [Travis CI](//travis-ci.org/wandersonwhcr/balance) para analisar o código. Inúmeros testes serão feitos, principalmente de padronização de código. Somente quando você receber um _status_ **passing**, crie o PR.

### Aceitando o PR

Eu só vou aceitar PR que contenham todos estes requisitos! Poxa, eu estou tentando criar um projeto legal, então vamos criar uma estrutura legal, certo?

Todos os retornos de _branches_ terão uma mensagem com o nome do _branch_ utilizado para retorno. Isto quer dizer que vamos saber todos os pontos onde todos os _branches_ sairam e retornaram. Simples assim.

### Resumo

Como eu utilizo o repositório original para envio de novos _branches_, normalmente faço os seguintes comandos para a alteração de código-fonte da _issue_ 42.

```bash
git remote update --prune
git checkout master
git merge origin/master
git checkout master -b issue-42
```

Faço as alterações necessárias e para cada _commit_ faço os seguintes comandos.

```bash
git add -A
git commit -m'Issue #42 Resposta da vida, universo e tudo mais.'
```

Depois envio as alterações para o Github, que informará o Travis CI e este executará os testes unitários.

```bash
git push origin issue-42
```

Tudo certo? Feito! Vamos reintegrar o _branch_ no _develop_.

```bash
git remote update --prune
git checkout develop
git merge origin/develop

git checkout issue-42
git rebase develop
git checkout develop
git merge --no-ff issue-42

git push origin develop
```

Sim, faço _merges_ com `--no-ff` para criar um novo nó na árvore de versionamento.

Fechando versão? Então faço um _merge_ no _branch_ `master` do `develop` sem utilizar o `--no-ff`.

```bash
git remote update --prune

git checkout develop
git merge origin/develop

git checkout master
git merge origin/master

git merge develop
git push origin develop master
```

Simples?

## Finalização

Todos estes passos são necessários para mantermos um padrão. Sei que tudo isto parece complicado, mas foi a maneira mais simples que encontrei para manter todo o código-fonte padronizado.

Tens alguma dúvida? Entre em contato! Abra uma _issue_. Comente.

Happy Coding!
