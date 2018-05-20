# magento-to-opencart

> :warning: Isso **não** é uma extensão para migrar automaticamente produtos/categorias/clientes de uma loja para outra. Não deve ser utilizado por uma pessoa sem conhecimento em programação ou BD.

Script para exportar dados (produtos, categorias e clientes) de uma loja Magento para uma loja OpenCart.

Feito por um desenvolvedor que trabalha com a plataforma OpenCart e sem conhecimento avançado sobre Magento, apenas para atender uma necessidade.

Testado com:
* Magento 1.1
* OpenCart 2.3.0.2

### :thumbsup: O que este código faz:
* lê os dados dos produtos/categorias/clientes da loja Magento
* gera arquivos SQL compatíveis com o banco de dados da loja OpenCart (versão 2.3.x), para inserção dos dados lidos
* divide esse processo em partes, limitado a 50 itens de cada vez

### :x: O que este código não faz:
* caminho inverso (exportar dados do OpenCart para Magento)
* exportar as senhas dos clientes
* exportar informações adicionais ou avançadas de produtos, como opções, descontos ou atributos
* mover ou copiar as imagens dos produtos (arquivos podem ser movidos manualmente de `media/catalog/product` no Magento para `image/catalog` no OpenCart).
* inserir/alterar/excluir diretamente algum dado em qualquer uma das lojas

### Uso:
* colocar a pasta exportar na raiz da loja Magento
* a pasta exportar/output deve estar com permissão de escrita
* acessar pelo navegador **exportar/index.html**
* ao clicar em um dos botões (para exportar produtos, clientes ou categorias), os arquivos SQL serão gerados na pasta output
* cada etapa do processo exporta 50 itens, e vai passando para os próximos 50 automaticamente até acabar. Não é necessário clicar mais de uma vez em cada botão
* caso seja clicado mais de uma vez no mesmo botão, os arquivos SQL serão substituídos

### Observações:
* Dados de clientes inseridos são apenas para o país Brasil
* Os IDs dos campos personalizados dos clientes na loja OpenCart podem ser configurados nas constantes no arquivo `exportar/classes/Magento/Customer.php`
* O ID do idioma padrão para a geração dos arquivos SQL (`language_id`) é 2. Pode ser alterado nos seguintes arquivos:
  * `exportar/classes/OpenCart/Category.php`
  * `exportar/classes/OpenCart/Customer.php`
  * `exportar/classes/OpenCart/Product.php`
