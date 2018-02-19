# Compress Zip

## Requisitos

	- PHP > 5.6 (Preferência PHP 7)
	- NODE
	- COMPOSER

## Instalação

	- Clone o respositório

	- Composer update

	- Inicie o servidor local do php: php -S localhost:8000

## Estrutura e Configuração
	Navegue até o arquivo compress.php onde fica toda a configuração, na **variável $path** digite o caminho completo do diretório que deseja compactar.

	**Não feche o naveagor enquanto a aplicação estiver sendo executada. Você será notificado quando a tarefa estiver concluída.**

	**$name** está variável leva o nome que será dado ao arquivo após a tarefa ser concluída

	**$name_file** está variável leva o nome que será dado ao arquivo zipado após a tarefa ser concluída


## Excluir arquivo após download
	 Você pode excluir o arquivo após o download descomentando a linha 163 Função unlink(); no arquivo compress.php


## Alerta de tarefa concluída via E-mail

	Para receber um e-mail após a tarefa ser concluída, descomente a linha 133 Função SendMail();

	**IMPORTANTE**

	**Não esqueça de preencher os dados na função SendMail(); no arquivo compress.php, caso contrário nenhum e-mail será enviado**
