# Cropper @RafaelDms


###### Cropper is a component that simplifies the creation of JPG and PNG image thumbnails with a cache engine. Cropper CC creates your image for each part required in the application with zero complexity.

Cropper é um componente que simplifica a criação de miniaturas de imagens JPG e PNG com um motor de cache. O Cropper CC cria versões de suas imagens para cada dimensão necessária na aplicação com zero de complexidade.

#### Webp Thumbnails:

Published in version 1.0.* by default how thumbnails are converted to webP.

Adicionado na versão 1.0.* por padrão as miniaturas são convertidas para webP.

## About CoffeeCode

###### CoffeeCode is a set of small and optimized PHP components for common tasks. Held by Robson V. Leite and the UpInside team. With them you perform routine tasks with fewer lines, writing less and doing much more.

CoffeeCode é um conjunto de pequenos e otimizados componentes PHP para tarefas comuns. Mantido por Robson V. Leite e a equipe UpInside. Com eles você executa tarefas rotineiras com poucas linhas, escrevendo menos e fazendo muito mais.

### Highlights

- Simple Thumbnail Creator (Simples criador de miniaturas)
- Cache optimization per dimension (Otimização em cache por dimensão)
- Media Control by Filename (Contrôle de mídias por nome do arquivo)
- Cache cleanup by filename and total (Limpeza de cache por nome de arquivo e total)
- Composer ready and PSR-2 compliant (Pronto para o composer e compatível com PSR-2)

## Installation

Cropper is available via Composer:

```bash
"rafaeldms/cropper": "1.0.*"
```

or run

```bash
composer require rafaeldms/my-cropper
```

## Documentation

###### They are just two methods to do all the work. You just need to call ***make*** to create or use thumbnails of any size, or ***flush*** to free the cache of a file or the entire folder. CoffeeCode Cropper works like this:

São apenas dois métodos para fazer todo o trabalho. Você só precisa chamar o ***make*** para criar ou usar miniaturas de qualquer tamanho, ou o ***flush*** para liberar o cache de um arquivo ou da pasta toda. CoffeeCode Cropper funciona assim:

#### Create thumbnails

```php
<?php

$c = new \RafaelDms\Cropper\Cropper("patch/to/cache");

echo "<img src='{$c->make("images/image.jpg", 500)}' alt='Happy Coffee' title='Happy Coffee'>";
echo "<img src='{$c->make("images/image.jpg", 500, 300)}' alt='Happy Coffee' title='Happy Coffee'>";
```

#### Clear cache

```php
<?php

$c = new \RafaemDms\Cropper\Cropper("patch/to/cache");

//flush by filename
$c->flush("images/image.jpg");

//flush cache folder
$c->flush();
```

## License

The MIT License (MIT). Please see [License File](https://github.com/robsonvleite/cropper/blob/master/LICENSE) for more information.