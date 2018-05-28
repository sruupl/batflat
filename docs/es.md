General
=======

Batflat es un sistema de gestión de contenidos simple, ligero y rápido  polaco. Fue publicado por primera vez en mayo de 2016. La versión gratuita está compartida bajo [license](/license) que exige deja información sobre los autores y enlaces de referencia. Batflat funciona genial para la creación de sitios pequeños tales como presentaciones de negocios, portfolios, blogs o páginas de inicio. Con esta documentaciń aprenderás cómo instalar, configurar y crear tus propios módulos y temas.

La documentación está dividida en varias secciones. La primera es para instrucciones generales, la segunda para diseñadores web, y las dos últimas para desarrolladores web.


Prerrequisitos
--------------

Los prerrequisitos de sistema para Batflat son modestos ya que todos los modernos servidores pueden proporcionarlos.

+ Apache 2.2+ con `mod_rewrite`
+ PHP version 5.6+
+ Accesso a SQLite

La configuración de PHP debe tener las extensiones siguientes :

+ dom
+ gd
+ mbstring
+ pdo
+ zip
+ cURL


Instalación
------------

En primer lugar, descargar la última versión de [Batflat](http://feed.sruu.pl/batflat/download/latest).

Extraer todos los ficheros del paquete comprimido y colocarlo en el directorio local o del servidor remoto deseado. En el caso de un servidor remoto, conectar vía cliente (S)FTP, como el programa gratuito de [FileZilla](https://filezilla-project.org). Habitualmente los ficheros suelen ser subidos a carpetas llamadas `www`, `htdocs` o `public_html`.

**¡AVISO!** Asegúrese de que el fichero `.htaccess` está también en el servidor. Sin él el CMS no funcionará.

Algunos servidores pueden necesitar de permisos adicionales como `chmod 777` para los siguiente directorios y ficheros:

+ tmp/
+ uploads/
+ admin/tmp/
+ inc/data/
+ inc/data/database.sdb

Abra su navegador y localice la dirección donde los ficheros de Batflat están alojados. Debería ver una plantilla por defecto con contenido de ejemplo.

Vaya al panel de administración añadiendo `/admin/` al final de la URL. **El usuario y la contraseña iniciales son *"admin"*.** Debería cambiarlos justo despues que haya accedido por razones de seguridad. También recomendamos renombrar el directorio del panel de administración. *(necesitará cambiar el valor de la constante en el fichero de definiciones)*.


Configuración
-------------

CMS puede configurarse modificando los parámetros del panel de administración o mediante el fichero de definiciones. Aunque, no recomendamos cambiar la configuración del fichero si no es una persona con experiencia en ello.

### Panel de Administración
Para cambiar la configuración básica en el panel de administrador, seleccione la pestaña `Párametros`. Puede indicar un nombre de página, una descripción o palabras clave en las metaetiquetas, the meta tags, así como en cualquier parte de la plantilla por defecto, como en su cabecera. Puede cambiar la página de inicio, el idioma por defecto, *(indistintamente para el sitio web como para el panel)*, definir el contenido del pié de página, y elegir el editor *(HTML o WYSIWYG)* que estará disponible al editar subpáginas y artículos del blog.

Cambiará la configuración de los restantes módulos en las pestañas correspondientes con sus nombres.

### Fichero de definiciones
Más avanzadas cosas pueden cambiarse en el fichero `inc/core/defines.php`, el cual contiene definiciones de variables constantes.

+ `ADMIN` — el nombre del directorio que contiene el panel de administración
+ `THEMES` — ruta al directorio que contiene los temas
+ `MODULES` — ruta al directorio que contiene los módulos
+ `UPLOADS` — ruta al directorio que contiene los ficheros subidos
+ `FILE_LOCK` — bloque la posibilidad de modificar ficheros mediante el panel de administración
+ `BASIC_MODULES` — listado de los módulos básicos que no pueden eliminarse
+ `HTML_BEAUTY` — formato HTML embellecido tras parseo del código
+ `DEV_MODE` — modo desarrollador, donde los errores PHP y los avisos son mostrados


Actualizar
----------

Si desea estar al día con las últimas novedades, corrección de errores e incidencias de seguridad, deberá revisar regularmente las actualizaciones de Batflat. Puede hacerlo en `Parámetros -> Actualizaciones`. El sistema comprobará si hay una nueva versión y automáticamente descargará un nuevo paquete desde nuestro servidor que actualice los ficheros principales y los módulos.

En caso de complicaciones puede usar el modo manual. Para hacerlo así, descargue la última versión de Batflat, súbala al directorio principal de la aplicación, y, entonces, añada el parámetro `&manual` al final del marcador URL de actualización. El CMS debería detectar un paquete zipeado y al hacer click sobre el botón de actualización, el proceso de extracción y sobreescritura de ficheros se ejecutará.

Antes de cada actualización, Batflat crea una copia de seguridad. La encontrará en el directorio de scripts, en la carpeta de `backup/`. Si la actualización ha fallado, puede restaurarla en cualquier momento.


Temas
======

Estructura
----------

La estructura de los temas en Batflat es muy simple. Tan sólo cree una nueva carpeta en el directorio `themes/` con los ficheros siguientes:

+ `index.html` — plantilla por defecto para subpáginas
+ `manifest.json` — información del tema
+ `preview.png` — pantallazo que muestre el tema *(opcional)*

Cada subpágina puede usar otra plantilla, por lo que, además del mencionado fichero, puede también crear otror, p. ej. `xyz.html`. La selección de plantillas está disponible en el panel de administración mientras crea una página. No hay reglas sobre ficheros CSS y JS. Total libertad.

En la carpeta del tema puede también crear sus propias vistas del módulo. Para hacer ésto debe crear un directorio `modules/nombre_modulo` y ficheros `*.html` con los nombres correspondientes a los nombres de las vista originales. Por ejemplo, la vista del formulario de contacto debe estar contenida en la ruta siguiente: `themes/theme_name/modules/contact/form.html`. Batflat automáticamente detecta una nueva vista y la usa en lugar de la vista por defecto del módulo.


Etiquetas de Plantillas (Template tags)
---------------------------------------

CMS usa un sistema de plantillas sencillo que incluye las siguientes etiquetas:

### Variables
```php
{$foo}        // simple variable
{$foo|e}      // HTML escape for variable
{$foo|cut:10} // content of the variable cut to 10 characters
{$foo.bar}    // array
```
El acceso a los elementos del array se hace por el carácter '.' (punto).

### Condiciones
```php
{if: $foo > 5}
    lorem
{elseif: $foo == 5}
    ipsum
{else}
    dolor
{/if}
```

### Bucles (loop)
```html
<ul>
{loop: $foo}
    <li>{$key}, {$value}, {$counter}</li>
{/loop}

{loop: $foo as $bar}
    <li>{$key}, {$bar}, {$counter}</li>
{/loop}

{loop: $foo as $bar => $baz}
    <li>{$bar}, {$baz}, {$counter}</li>
{/loop}
</ul>
```
La etiqueta loop tiene 3 niveles de expansión. El primero is una variable array que el sistema de plantillas interrumpirá en tres variables llamadas `$key`,` $value` y `$counter`, las cuales contienen sucesivas iteraciones inicializadas desde cero. El segundo paso permite indicar el el nombre de la variable que contiene el valor, y el tercero es también el nombre de la variable índice.

### Incluir ficheros de plantilla
```html
<html>
    <body>
    {template: header.html}
    <main>
        <p>Lorem ipsum dolor sit amet.</p>
    </main>
    {template: footer.html}
    </body>
</html>
```

### Código PHP
```php
Today&grave;s date: {?= date('Y-m-d') ?}
```
Si deja el carácter `=`, el código sólo se ejecutará y no mostrará nada. Esto permite, por ejemplo, definir nuevas variables dentro de la plantilla:
```php
{? $foo = 5 ?}
```

### Deshabilitar intérprete (parseo)
```
{noparse}Use the {$ contact.form} tag to display contact form.{/noparse}
```
Cualquier etiqueta dentro de la expresión *noparse* se mantendrá sin variaciones.

### Comentarios
```
{* this is a comment *}
```
Los Comentarios no serán visibles en el código fuente tras compliarse la plantilla.

### Idiomas
```
{lang: pl_polski}
    Witaj świecie!
{/lang}
{lang: en_english}
    Hello world!
{/lang}
{lang: es_spanish}
    ¡Hola, mundo!
{/lang}
```
Si desea personalizar los elementos de la plantilla en un idioma particular, utilice las etiquetas susodichas.


Sistema de variables
--------------------
Batflat, como sus módulos, proporciona muchas variables *(habitualmente arrays)* que sirven para mostrar cada elemento de la página. He aquí algunos de los más importantes:

+ `{$settings.pole}` — un elemento array conteniendo el valor del parámetro dado por Batflat
+ `{$settings.moduł.pole}` — un elemento array conteniendo el valor del parámetro dado por el módulo
+ `{$bat.path}` — almacena la ruta donde se aloja el sistema
+ `{$bat.lang}` — muestra el idioma actual utilizado
+ `{$bat.notify}` — la última modificación
+ `{$bat.notify.text}` - texto de notificación
+ `{$bat.notify.type}` - tipo de mensaje correspondiente a las Bootstrap *(danger, success)*
+ `{$bat.header}` —  metaetiquetas adicionales, scripts JS y hojas de estilo CSS cargadas por los módulos
+ `{$bat.footer}` — scripts JS adicionales cargados por los módulos
+ `{$bat.theme}` — muestra la ruta al tema activo con el servidor
+ `{$bat.powered}` — muestra *Powered by Batflat* con un enlace al sitio oficial
+ `{$navigation.xyz}` — muestra una lista de `<li>` elementos de navegación
+ `{$page.title}` — muestra el nombre de la subpágina
+ `{$page.content}` — muestra el contenido de la subpágina

Ejemplo
-------

### manifest.json

```
{
    "name": "Example",
    "version": "1.0",
    "author": "Bruce Wayne",
    "email": "contact@waynecorp.com",
    "thumb": "preview.png"
}
```

### index.html

```html
<!doctype html>

<html>
<head>
  <meta charset="utf-8">
  <title>{$page.title} - {$settings.title}</title>
  <meta name="description" content="{$settings.description}">
  <meta name="keywords" content="{$settings.keywords}">
  <link rel="stylesheet" href="{$bat.theme}/styles.css">
  {loop: $bat.header}{$value}{/loop}
</head>

<body>
    <nav>
        <ul>
            {$navigation.main}
        </ul>
    </nav>

    <main>
        <h1>{$page.title}</h1>
        {$page.content}
    </main>

    <footer>
        {$settings.footer} {$bat.powered}
    </footer>

    <script src="{$bat.theme}/scripts.js"></script>
    {loop: $bat.footer}{$value}{/loop}
</body>
</html>
```

Módulos
=======

Estructura
-----------

Cada módulo, como los temas, deben estar en carpetas separadas creadas en el directorio `inc/modules/`. Por favor, observe qeu el directorio no contiene ni mayúsculas ni caracteres especiales, tales como espacios.

Cuando cree un módulo, reflexione sobre qué el tipo de módulo desea usar. ¿Será configurable en el panel de administración? ¿o sólo funcionará en el lado de presentación? Debido a esta división, en Batflat distinguimos tres archivos principales del módulo:

+ `Info.php` — contiene información sobre el módulo, tal como nombre, descripción, autor o icon
+ `Admin.php` — el contenido de este fichero será accessible mediante el panel de administración
+ `Site.php` — el contenido de este fichero estará disponible a los visitantes del sitio

El cuarto y opcional fichero es el `ReadMe.md` que debería contener información adicional para futuros usuarios en formato [Markdown](https://es.wikipedia.org/wiki/Markdown), p.ej. cómo usar el módulo.

Si planea escribir un módulo que use HTML, sería bueno que se asegure que el código PHP está separado del lenguaje HTML en sí mismo. Para ello, necesita crear un directorio `views` dentro de la carpeta del módulo. Incluya cualquier fichero de vistas en él.

El problema multidioma del módulo es similar. Tan sólo cree ficheros de idioma con la extensión `ini` dentro del directorio `lang`.

La estructura del módulo debiera verse algo parecido a ésto:
```
ejemplo/
|-- lang/
|    |-- admin/
|    |    |-- en_english.ini
|    |    |-- pl_polski.ini
|    |    |-- es_spanish.ini
|    |-- en_english.ini
|    |-- pl_polski.ini
|    |-- es_spanish.ini
|-- views/
|    |-- admin/
|    |    |-- bar.html
|    |-- foo.html
|-- Admin.php
|-- Info.php
|-- Site.php
+-- ReadMe.md
```

Crear un módulo
-----------------

### Info file

El fichero más importante para cada módulo. Contiene información básica e instrucciones durante la instalación y desinstalación.

```php
<?php

    return [
        'name'          =>  'Example',
        'description'   =>  'Lorem ipsum....',
        'author'        =>  'Robin',
        'version'       =>  '1.0',
        'compatibility' =>  '1.3.*',                    // Compatibility with Batflat version
        'icon'          =>  'bolt',

        'pages'         =>  ['Example' => 'example'],   // Registration as a page (optional)

        'install'       =>  function() use($core)       // Install commands
        {
            // lorem ipsum...
        },
        'uninstall'     =>  function() use($core)       // Uninstall commands
        {
            // lorem ipsum...
        }
    ];
```

Una lista de iconos que puede usar en este fichero esta disponible en [fontawesome.io](http://fontawesome.io/icons/). Asegúrese de que no se indica el nombre del icono con el prefijo `fa-`.

Registrar un módulo como una página le permite, libremente, usar el enrutamiento y su selección como página de inicio.


### Fichero Admin

El contenido de este fichero se lanzará en el panel de administración.

```php
<?php
    namespace Inc\Modules\Ejemplo;

    use Inc\Core\AdminModule;

    class Admin extends AdminModule
    {
        public function init()
        {
            // Procedures invoked at module initialization
        }

        public function navigation()
        {
            return [
                'Foo'   => 'foo',
                'Bar'   => 'bar',
            ];
        }

        public function getFoo($parm)
        {
            return "Foo $parm!";
        }

        public function postBar()
        {
            return "Bar!";
        }
    }
```

En el método `navigation`, incluir un array con las subpáginas del módulo. Cada página debería ser asignada a un método *(sin un prefijo)*. Los elementos de este array se mostrarán en el menú del panel de administración.

Los métodos también aceptan argumentos que son pasados vía URL. Por ejemplo, tras introducir la dirección `/ejemplo/foo/abc`, el método `getFoo` devolverá *"Foo abc!"*.

Como puede ver en la siguiente lista, cada método que representa la subpágina del módulo debería tener un prefijo especificando el tipo de solicitud. En muchos casos usará la nomenclatura `getFoo`, y el formulario de envío `postFoo`. Si el método soporta todos los tipos, debería preceder el prefijo `any` *(por ejemplo, `anyFoo`)*. Esto es importante porque páginas sin prefijo no serán manejadas. Los métodos soportados son traducidos por enrutado dinámico como sigue:

+ `getFoo()` — como `/ejemplo/foo` para una petición GET
+ `getFoo($parm)` — como `/ejemplo/foo/abc` para una petición GET
+ `postBar()` — como `ejemplo/bar` para peticiones POST *(formulario de envío)*
+ `anyFoo()` — como `/ejemplo/foo` para cada tipo de petición

### Site file

Este fichero se responsabiliza de la parte vista por los visitantes del sitio. Si el módulo es demasiado largo, una buena práctica es registrarlo como una página y aplicarle un enrutado.

```php
<?php

    namespace Inc\Modules\Ejemplo;

    use Inc\Core\SiteModule

    class Site extends SiteModule
    {
        public function init()
        {
            $this->_foo();
        }

        public function routes()
        {
            $this->route('ejemplo', '_mySite');
        }

        private function _mySite()
        {
            $page = [
                'title' => 'Título ejemplo..',
                'desc' => 'Descripción del sitio',
                'content' => 'Lorem ipsum dolor...'
            ];

            $this->setTemplate('index.html');
            $this->tpl->set('page', $page);
        }

        private function _foo()
        {
            $this->tpl->set('bar', '¿Por qué tan serio?');
        }
    }
```

En el anterior ejemplo, una nueva variable de plantilla `bar` se ha creado la cual, por llamada al método `_foo()` en el módulo initializer, puede usarse en los ficherso del tema como `{$bar}`. En suma, el método `routes()` ha creado una subrutina `/ejemplo` que apunta a la llamada del método `_mySite()`. Si va a `http://example.com/ejemplo`, llamará al método `_mySite()`.

### Fichero de Idiomas

El módulo puede contener variables de idiomas que pueden usarse en clases y vistas. Los ficheros de idiomas tienen una extension `.ini` y están localizados en el directorio` lang` del módulo.
Por ejemplo, si deseas añadir un fichero de idioma conteniendo expresiones en Español para la parte administrativa del módulo `Ejemplo`, deberá crear un fichero en la ruta `inc/modules/example/lang/admin /es_spanish.ini`.
El contenido debería asemejarse al siguiente listado:

```
full_name           = "Nombre y apellidos"
email               = "Correo"
subject             = "Asunto"
message             = "Mensaje"
send                = "Enviar"
send_success        = "Correo correctamente enviado. Le responderemos pronto."
send_failure        = "Imposible enviar mensaje. Quizál la función mail() está deshabilitada en el servidor."
wrong_email         = "La dirección de correo enviada es incorrecta."
empty_inputs        = "Rellene todos los campos necesarios para enviar un mensaje."
antiflood           = "Debe esperar un rato antes de volver a enviar otro mensaje."
```

Utilice la construcción `$this->lang('subject')` en la clase del módulo y `{$lang.example.subject}` en la vista. Para una clase, podemos dejar el segundo parámetro del método `lang`, el cual será el nombre del módulo.


Enrutado
--------

Enrutado es el proceso de tramitar una petición de dirección recibida y decidir que debería ejecutarse o mostrarse. Se supone que llama al método/función adecuado en base a la URL de la página. Debe usar el enrutado dentro del método público `routes()`.

```php
void route(string $pattern, mixed $callback)
```

El primer parámetro del método `route` es una expresión regular. Algunas de tales expresiones ya se han definido:

+ `:any` — cualquier cadena de carácteres (string)
+ `:int` — números enteros
+ `:str` — una string que es una etiqueta del tipo slug

El segundo parámetro es un nombre de método  o una función anónima que pasa un número de argumentos definidos en una expresión regular.

#### Ejemplo
```php
public function routes()
{
    // URL: http://example.com/blog

    // - by calling the method inside the module:
    $this->route('blog', 'importAllPosts');

    // - by calling an anonymous function:
    $this->route('blog', function() {
        $this->importAllPosts();
    });

    // URL: http://example.com/blog/2
    $this->route('blog/(:int)', function($page) {
        $this->importAllPosts($page);
    });

    // URL: http://example.com/blog/post/lorem-ipsum
    $this->route('blog/post/(:str)', function($slug) {
        $this->importPost($slug);
    });

    // URL: http://example.com/blog/post/lorem-ipsum/4
    $this->route('blog/post/(:str)/(:int)', function($slug, $page) {
        $this->importPost($slug, $page);
    });
}
```


Métodos
-------

Los módulos muestran fachadas especiales que facilitan el acceso a los métodos dentro del núcleo (core). Esto permite acortar las llamadas como `$this->core->foo->bar`.

### db

```php
void db([string $table])
```

Permite operar en una base de datos. Los detalles se describen en la sección del núcleo (core).

#### Argumentos
+ `table` — Database table name *(opcional)*

#### Ejemplo
```php
$this->db('table')->where('age', 20)->delete();
```


### draw

```php
string draw(string $file [, array $variables])
```

Devuelve el código compilado de la vista que, previamente, ha empleado las etiquetas del sistema de plantillas. También permite definir variables por sustitución con el método `set()`.

#### Argumentos
+ `file` — fichero con la vista dentro del módulo o la ruta a un fichero fuera de él.
+ `variables` — un array de definición de variables que pueden usarse como etiquetas *(opcional)*

#### Ejemplo
```php
// Compilation of the view inside the module
$this->draw('form.html', ['form' => $this->formFields]);

// Compilation of the view outside the module
$this->draw('../path/to/view.html', ['foo' => 'bar']);
```


### lang

```php
string lang(string $key [, string $module])
```

Devuelve el contenido de la palabra clave en el idioma correspondiente para el módulo actual o, si se indica, para el módulo referenciado.

#### Argumentos
+ `key` — el nombre de la clave del array del idioma
+ `module` — el nombre del módulo desde el que se quiere seleccionar la clave *(opcional)*

#### Ejemplo
```php
// Reference to local translation
$this->lang('foo');                 // $this->core->lang['module-name']['foo'];

// Reference to general translation
$this->lang('cancel', 'general');   // $this->core->lang['general']['cancel'];

// Reference to the translation of "pages" module
$this->lang('slug', 'pages')        // $this->core->lang['pages']['slug'];
```


### notify

```php
void notify(string $type, string $text [, mixed $args [, mixed $... ]])
```

Permite llamadas a notificaciones para el usuario.

#### Argumentos
+ `type` — tipo de notificación: *success* or *failure*
+ `text` — contenido de la notificación
+ `args` — arguemntos adicionales *(opcional)*

#### Ejemplo
```php
$foo = 'Bar';
$this->notify('success', 'This is %s!', $foo); // $this->core->setNotify('success', 'This is %s!', $foo);

```


### settings

```php
mixed settings(string $module [, string $field [, string $value]])
```

Obtiene o establece el valor del módulo de parámetros.

#### Argumentos
+ `module` — nombre del módulo y, opcionalmente, campo separado por un período
+ `field` — nombre del campo del módulo *(opcional)*
+ `value` — el valor que el campo módulo cambiará *(opcional)*

#### Ejemplo
```php
// Select the "desc" field from the "blog" module
$this->settings('blog.desc');    // $this->core->getSettings('blog', 'desc');

// Select the "desc" field from the "blog" module
$this->settings('blog', 'desc'); // $this->core->getSettings('blog', 'desc');

// Set the content of the "desc" field from the "blog" module
$this->settings('blog', 'desc', 'Lorem ipsum...');
```

### setTemplate

```php
void setTemplate(string $file)
```

Permite cambiar el fichero de la plantilla de presentación. Este método funciona sólo en la clase `Site`.

#### Argumentos
+ `file` — El nombre del fichero de la plantilla

#### Ejemplo
```php
$this->setTemplate('index.html'); // $this->core->template = 'index.html';
```


Núcleo (Core)
=============

Este es el kernel/motor de Batflat, la parte más importante que se responsabiliza de todas sus tareas básicas. El core contiene muchas definiciones de constantes, funciones, y métodos que se pueden usar al escribir módulos.

Constantes
----------

Todas las definiciones de constantes se describen en la primera parte de esta documentación. Para usarlas en un fichero PHP tan sólo llame a sus nombres. Las constantes son particularmente útiles al crear URLs y rutas de ficheros.

#### Ejemplo
```php
echo MODULES.'/contact/view/form.html';

```


Funciones
---------

Batflat tiene varias funciones de ayuda incorporada (built-in helper functions) que facilitan la creación de módulos.

### domain

```php
string domain([bool $with_protocol = true])
```

Devuelve el nombre de dominio con o sin http(s).

#### Argumentos
+ `with_protocol` — decide si la dirección será devuelta con o sin protocolo

#### Valor devuelto
String con el nombre de dominio.

#### Ejemplo
```php
echo domain(false);
// Result: example.com
```


### checkEmptyFields

```php
bool checkEmptyFields(array $keys, array $array)
```

Comprueba si el array contiene elementos vacíos. Es útil al valir formularios.

#### Argumentos
+ `keys` — lista de elementos del array que la función ha revisado
+ `array` — source array

#### Valor devuelto
Devuelve `TRUE` si, al menos, uno de los elementos está vacío. `FALSE` cuando todos los elementos están completos.

#### Ejemplo
```php
if(checkEmptyFields(['name', 'phone', 'email'], $_POST) {
    echo 'Fill in all fields!';
}
```


### currentURL

```php
string currentURL()
```

Devuelve la actual URL.

#### Ejemplo
```php
echo currentURL();
// Result: http://example.com/contact
```


### createSlug

```php
string createSlug(string $text)
```

Traduce texto en caracteres no alfanuméricos, guiones y espacios, y elimina caracteres especiales. Se usar para crear barras en URLs y nombres de variables en el sistema de plantillas.

#### Argumentos
+ `text` — texto a convertir

#### Valor devuelto
Devuelve el texto en formato amigable.

#### Ejemplo
```php
echo createSlug('To be, or not to be, that is the question!');
// Result: to-be-or-not-to-be-that-is-the-question
```


### deleteDir

```php
bool deleteDir(string $path)
```

Función recursiva que elimina el directorio y todos sus contenidos.

#### Argumentos
+ `path` — ruta del directorio

#### Valor devuelto
Devuelve `TRUE` si ha iod bien, `FALSE` si ha fallado.

#### Ejemplo
```php
deleteDir('foo/bar');
```


### getRedirectData
```php
mixed getRedirectData()
```

Devuelve los datos pasados a la sesión al usar `redirect()`.

#### Valor devuelto
Un array o `null`.

#### Ejemplo
```php
$postData = getRedirectData();
```


### htmlspecialchars_array

```php
string htmlspecialchars_array(array $array)
```

Sustituye caracteres especiales de los elementos de un array en entidades HTML.

#### Argumentos
+ `array` — el array que se convertirá

#### Valor devuelto
Devuelve el texto convertido.

#### Ejemplo
```php
$_POST = htmlspecialchars_array($_POST);
```


### isset_or

```php
mixed isset_or(mixed $var [, mixed $alternate = null ])
```

Sustituye una variable vacía con un valor alternativo.

#### Argumentos
+ `var` — variable
+ `alternate` — valor sustitutorio de la variable *(opcional)*

#### Valor devuelto
Devuelve un valor alternativo.

#### Ejemplo
```php
$foo = isset_or($_GET['bar'], 'baz');
```


### parseURL
```php
mixed parseURL([ int $key = null ])
```

Parsea la actual URL del script.

#### Argumentos
+ `key` — Número del parámetro de la URL *(opcional)*

#### Valor devuelto
Un array o su elemento individual.

#### Ejemplo
```php
// URL: http://example.com/foo/bar/4

var_dump(parseURL())
// Result:
// array(3) {
//   [0] =>
//   string(3) "foo"
//   [1] =>
//   string(3) "bar"
//   [2] =>
//   int(4)
// }

echo parseURL(2);
// Result: "bar"
```


### redirect

```php
void redirect(string $url [, array $data = [] ])
```

Redirección a la URL específica. Permite guardar datos desde el array a la sesión. Es útil para memorizar datos no guardados de los formularios.

#### Argumentos
+ `url` — dirección a la que redireccionar
+ `data` — un array que será pasado a la sesión *(opcional)*

#### Ejemplo
```php
redirect('http://www.example.com/');

// Save the array to session:
redirect('http://www.example.com/', $_POST);
```


### url
```php
string url([ mixed $data = null ])
```

Crer una URL absoluta. El panel de administración automáticamente añade un token.

#### Argumentos
+ `data` — string o array

#### Valor devuelto
URL absoluta.

#### Ejemplo
```php
echo url();
// Result: http://example.com

echo url('foo/bar')
// Result: http://example.com/foo/bar

echo url('admin/foo/bar');
// Result: http://example.com/admin/foo/bar?t=[token]

echo url(['admin', 'foo', 'bar']);
// Result: http://example.com/admin/foo/bar?t=[token]
```


Métodos
-------

Además de las funciones, existen varios métodos importantes que aceleran el proceso de creación de nuevas funcionalidades del sistema.

### addCSS

```php
void addCSS(string $path)
```

Importa el fichero CSS en la cabecera del tema.

#### Argumentos
+ `path` — URL al fichero

#### Ejemplo
```php
$this->core->addCSS('http://example.com/style.css');
// Result: <link rel="stylesheet" href="http://example.com/style.css" />
```


### addJS

```php
void addJS(string $path [, string $location = 'header'])
```

Importa el fichero JS en la cabecera o al pié del tema.

#### Argumentos
+ `path` — URL al fichero.
+ `location` — *header* o *footer* *(opcional)*

#### Ejemplo
```php
$this->core->addJS('http://example.com/script.js');
// Result: <script src="http://example.com/script.js"></script>
```


### append

```php
void append(string $string, string $location)
```

Añade un string a la cabecera o al pié de la página.

#### Argumentos
+ `string` — cadena de caracteres
+ `location` — *header* o *footer*

#### Ejemplo
```php
$this->core->append('<meta name="author" content="Bruce Wayne">', 'header');
```


### getModuleInfo

```php
array getModuleInfo(string $dir)
```

Devuelve información del módulo. Este método funciona sólo en la clase `Admin`.

#### Argumentos
+ `name` — nombre del directorio del módule

#### Valor devuelto
Array con las informaciones.

#### Ejemplo
```php
$foo = $this->core->getModuleInfo('contact');
```


### getSettings

```php
mixed getSettings([string $module = 'settings', string $field = null])
```

Obtienes el valor de los parámetros del módulo. Por defecto son los parámetros generales de Batflat.

#### Argumentos
+ `module` — nombre del módulo *(opcional)*
+ `field` — campo con la definición del parámetro *(opcional)*

#### Valor devuelto
Array o string.

#### Ejemplo
```php
echo $this->core->getSettings('blog', 'title');
```


### getUserInfo

```php
string getUserInfo(string $field [, int $id ])
```

Devuelve información sobre el usuario registrado o el usuario con la ID obtenida. Este método funciona sólo en la clase `Admin`.

#### Argumentos
+ `field` — nombre del campo en la base de datos
+ `id` — número de ID *(opcional)*

#### Valor devuelto
El string del campo seleccionado.

#### Ejemplo
```php
// The currently logged in user
$foo = $this->core->getUserInfo('username');

// User with given ID
$foo = $this->core->getUserInfo('username', 1);
```


### setNotify

```php
void setNotify(string $type, string $text [, mixed $args [, mixed $... ]])
```

Genera una notificación.

#### Argumentos
+ `type` — tipo de notificación: *success* o *failure*
+ `text` — contenido de la notificación
+ `args` — argumentos adicionales *(opcional)*

#### Ejemplo
```php
$foo = 'Bar';
$this->core->setNotify('success', 'This is %s!', $foo);
// Result: "This is Bar!"
```


Base de datos
-------------

La base de datos que usa Batflat es SQLite version 3. Para su uso el CMS utiliza una simple clase que facilita la creación de consultas. No es necesario saber de SQL para ser capaz de operar con ello.

Además, recomendamos la herramienta [phpLiteAdmin](https://github.com/sruupl/batflat-pla) para la gestión de bases de datos. Es un script en un único fichero PHP similar a *phpMyAdmin*, donde se pueden administrar tablas de Batflat. Le permitirá familiarizarse con la estructura de las tablas existentes.
El fichero de la base de datos está ubicado en `inc/data/database.sdb`.


### SELECT

Seleccionar multiple registros:

```php
// JSON
$rows = $this->core->db('table')->toJson();

// Array
$rows = $this->core->db('table')->select('foo')->select('bar')->toArray();

// Object
$rows = $this->core->db('table')->select(['foo', 'b' => 'bar'])->toObject();
```

Seleccionar un único registro:
```php
// JSON
$row = $this->core->db('table')->oneJson();

// Array
$row = $this->core->db('table')->select('foo')->select('bar')->oneArray();

// Object
$row = $this->core->db('table')->select(['foo', 'b' => 'bar'])->oneObject();
```


### WHERE

Seleccionar un registro con el número indicado en la columna `id`:

```php
$row = $this->core->db('table')->oneArray(1);
// or
$row = $this->core->db('table')->oneArray('id', 1);
// or
$row = $this->core->db('table')->where(1)->oneArray();
// or
$row = $this->core->db('table')->where('id', 1)->oneArray();
```

Condiciones de complejidad:
```php
// Fetch rows whose column value 'foo' is GREATER than 4
$rows = $this->core->db('table')->where('foo', '>', 4)->toArray();

// Fetch rows whose column value 'foo' is GREATER than 4 and LOWER than 8
$rows = $this->core->db('table')->where('foo', '>', 4)->where('foo', '<', 8)->toArray();
```

OR WHERE:
```php
// Fetch rows whose column value 'foo' is EQUAL 4 or 8
$rows = $this->core->db('table')->where('foo', '=', 4)->orWhere('foo', '=', 8)->toArray();
```

WHERE LIKE:
```php
// Fetch rows whose column 'foo' CONTAINS the string 'bar' OR 'bases'
$rows = $this->core->db('table')->like('foo', '%bar%')->orLike('foo', '%baz%')->toArray();
```

WHERE NOT LIKE:
```php
// Fetch rows whose column 'foo' DOES NOT CONTAIN the string 'bar' OR 'baz'
$rows = $this->core->db('table')->notLike('foo', '%bar%')->orNotLike('foo', '%baz%')->toArray();
```

WHERE IN:
```php
// Fetch rows whose column value 'foo' CONTAINS in array [1,2,3] OR [7,8,9]
$rows = $this->core->db('table')->in('foo', [1,2,3])->orIn('foo', [7,8,9])->toArray();
```

WHERE NOT IN:
```php
// Fetch rows whose column value 'foo' DOES NOT CONTAIN in array [1,2,3] OR [7,8,9]
$rows = $this->core->db('table')->notIn('foo', [1,2,3])->orNotIn('foo', [7,8,9])->toArray();
```

Condiciones de agrupamiento:
```php
// Fetch rows those column value 'foo' is 1 or 2 AND status is 1
$rows = $this->core->db('table')->where(function($st) {
            $st->where('foo', 1)->orWhere('foo', 2);
        })->where('status', 1)->toArray();
```

Operadores de comparaciones permitidos: `=`, `>`, `<`, `>=`, `<=`, `<>`, `!=`.


### JOIN

INNER JOIN:
```php
$rows = $this->core->db('table')->join('foo', 'foo.table_id = table.id')->toJson();
```

LEFT JOIN:
```php
$rows = $this->core->db('table')->leftJoin('foo', 'foo.table_id = table.id')->toJson();
```


### HAVING

```php
$rows = $this->core->db('table')->having('COUNT(*)', '>', 5)->toArray();
```

OR HAVING:
```php
$rows = $this->core->db('table')->orHaving('COUNT(*)', '>', 5)->toArray();
```


### INSERT

El método `save` puede añadir un nuevo registro a la tabla o actualizar uno existente si tiene tal condición.
- Cuando se añade un nuevo registro, el número de identificación será el valor devuelto.
- Cuando se actualiza un registro existente, un booleano será el valor devuelto.

```php
// Add a new record
$id = $this->core->db('table')->save(['name' => 'James Gordon', 'city' => 'Gotham']);
// Return value: ID number of new record

// Update an existing record
$this->core->db('table')->where('age', 50)->save(['name' => 'James Gordon', 'city' => 'Gotham']);
// Return value: TRUE on success or FALSE on failure
```


### UPDATE

Actualizar registros, en caso de éxito se devuelve `TRUE`. Sino, se devuelve `FALSE`.

```php
// Changing one column
$this->core->db('table')->where('city', 'Gotham')->update('name', 'Joker');

// Changing multiple columns
$this->core->db('table')->where('city', 'Gotham')->update(['name' => 'Joker', 'type' => 'Villain']);
```


### SET

```php
$this->core->db('table')->where('age', 65)->set('age', 70)->set('name', 'Alfred Pennyworth')->update();
```


### DELETE

La eliminación correcta de un registro devolverá su número de identificación.

```php
// Delete record with `id` equal to 1
$this->core->db('table')->delete(1);

// Deletion of record with condition
$this->core->db('table')->where('age', 20)->delete();
```


### ORDER BY

Orden Ascendente:
```php
$this->core->db('table')->asc('created_at')->toJson();
```

Orden Descendente:
```php
$this->core->db('table')->desc('created_at')->toJson();
```

Orden Combinado:
```php
$this->core->db('table')->desc('created_at')->asc('id')->toJson();
```


### GROUP BY

```php
$this->core->db('table')->group('city')->toArray();
```


### OFFSET, LIMIT

```php
// Fetch 5 records starting at tenth
$this->core->db('table')->offset(10)->limit(5)->toJson();
```


### PDO

No todas las consulta se pueden crear usando los métodos anteriores *(p.ej. crear o eliminar una tabla)*, por lo que se puede también escribir consultas utilizando [PDO](http://php.net/manual/es/book.pdo.php):

```php
$this->core->db()->pdo()->exec("DROP TABLE `example`");
```


Sistema de plantillas (Template system)
----------------------------------------

Operar con el sistema de plantillas es fácil y se basa fundamentalmente en dos métodos. Uno permite asignar variables, mientras que el otro devuelve el código compilado. En situaciones exceptionales, los otros dos métodos son útiles.

### set

```php
void set(string $name, mixed $value)
```

Asigna un valor a una variable que puede usarse en las vistas.

#### Argumentos
+ `name` — nombre de la variable
+ `value` — valor de la variable

#### Ejemplo
```php
$foo = ['bar', 'baz', 'qux'];
$this->tpl->set('foo', $foo);
```


### draw

```php
string draw(string $file)
```

Devuelve el código compilado de la vista que, previamente, ha empleado las etiquetas del sistema de plantillas.

#### Argumentos
+ `file` — ruta del fichero

#### Valor devuelto
Un string, es decir, una vista compilada.

#### Ejemplo
```php
$this->tpl->draw(MODULES.'/galleries/view/admin/manage.html');
```


### noParse

```php
string noParse(string $text)
```

Protege ante la compilación de etiquetas del sistema de plantillas.

#### Argumentos
+ `text` — string a dejar intacta

#### Ejemplo
```php
$this->tpl->noParse('Place this tag in website template: {$contact.form}');
```


### noParse_array

```php
array noParse_array(array $array)
```

Protege ante la compilación de etiquetas del sistema de plantillas dentro del array.

#### Argumentos
+ `array` — array a dejar intacto

#### Ejemplo
```php
$this->tpl->noParse_array(['{$no}', '{$changes}']);
```

Idiomas
-------

Todos los ficheros de idiomas están ubicados en los directorios `lang` dentro del módulo y en la ruta `inc/lang`.
En esta última ruta hay las correspondientes carpetas para los idiomas con el siguiente formato: `en_english`. La primera parte es la abreviatura del idioma y la segunda es el nombre completo en inglés.
Dentro del directorio está el fichero `general.ini`, que contiene variables generales del idioma para el sistema.
Tras crear una nueva carpeta de idioma, Batflat automáticamente detecta el idioma añadido y permite su selección en el panel de administración. Tenga en cuenta que el proceso de creación de un nuevo idioma deberá repetirse para cada módulo.
