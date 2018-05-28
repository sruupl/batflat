Ogólne
======

Batflat to polski system zarządzania treścią, który jest prosty, lekki i szybki. Po raz pierwszy został wydany w maju 2016. Darmowa wersja aplikacji jest udostępniona w ramach [licencji](/license), która wymaga pozostawienia informacji o autorach oraz linku zwrotnego. Batflat sprawdza się świetnie podczas tworzenia małych serwisów, takich jak wizytówki firmowe, portfolia, blogi czy strony domowe. Dzięki tej dokumentacji dowiesz się jak go zainstalować, skonfigurować, a także jak tworzyć własne moduły i motywy.

Dokumentacja została podzielona na kilka sekcji. Pierwsza dotyczy ogólnych instrukcji, druga jest skierowana do web designerów, a dwie ostatnie do web deweloperów.


Wymagania
---------

Wymagania systemowe dla Batflata są skromne, zatem powinien je spełnić każdy nowoczesny serwer WWW:

+ Apache 2.2+ z trybem `mod_rewrite`
+ PHP w wersji 5.6+
+ Dostęp do SQLite

Konfiguracja PHP musi posiadać następujące rozszerzenia:

+ dom
+ gd
+ mbstring
+ pdo
+ zip
+ cURL


Instalacja
-----------

Na początek należy pobrać najnowszą wersję [Batflata](http://feed.sruu.pl/batflat/download/latest).

Wypakuj wszystkie pliki ze skompresowanej paczki, a następnie przenieś je do katalogu lokalnego lub zdalnego serwera. W przypadku serwera zdalnego, należy połączyć się z nim poprzez klienta (S)FTP, np. darmowym programem [FileZilla](https://filezilla-project.org). Zazwyczaj pliki należy wgrać do folderu `www`, `htdocs` lub `public_html`.

**Uwaga!** Upewnij się, że plik `.htaccess` również znalazł się na serwerze. Bez niego skrypt nie będzie działał.

Niektóre serwery mogą wymagać nadania dodatkowych uprawnień `chmod 777` dla następujących katalogów i plików:

+ tmp/
+ uploads/
+ admin/tmp/
+ inc/data/
+ inc/data/database.sdb

Otwórz przeglądarkę i przejdź do adresu, w którym zlokalizowane są pliki Batflata. Powinien ukazać się domyślny szablon wraz z przykładową treścią.

Aby przejść do panelu administracyjnego, dodaj `/admin/` na koniec adresu. **Początkowy login i hasło to *"admin"*.** Wypada je zmienić zaraz po zalogowaniu ze względów bezpieczeństwa. Zalecamy również zmianę nazwy katalogu z panelem administracyjnym *(trzeba wtedy zmienić wartość stałej w pliku defines)*.


Konfiguracja
------------

CMS można skonfigurować poprzez edycję ustawień w panelu administracyjnym oraz poprzez plik z definicjami. Nie zalecamy jednak zmieniać konfiguracji w pliku, jeżeli jesteś osobą niedoświadczoną.

### Panel administracyjny
Aby zmienić podstawową konfigurację w panelu administracyjnym, wybierz zakładkę `Ustawienia`. Możesz wprowadzić tam nazwę strony, jej opis czy słowa kluczowe, które będą zawarte w meta-tagach, a także w innych miejscach szablonu domyślnego, np. w nagłówku. Zmienisz tutaj również stronę startową, domyślny język *(zarówno dla strony jak i panelu)*, zdefiniujesz treść stopki oraz wybierzesz rodzaj edytora *(HTML lub WYSIWYG)*, który będzie dostępny podczas edycji podstron oraz wpisów na blogu.

Konfigurację pozostałych modułów zmienisz w zakładkach odpowiadających ich nazwie.

### Plik defines
Bardziej zaawansowane rzeczy możesz zmienić w pliku `inc/core/defines.php`, który zawiera definicje stałych wartości. 

+ `ADMIN` — nazwa katalogu zawierająca panel administracyjny
+ `THEMES` — ścieżka do katalogu zawierającego motywy
+ `MODULES` — ścieżka do katalogu zawierającego moduły
+ `UPLOADS` — ścieżka do katalogu zawierającego wgrane pliki
+ `FILE_LOCK` — blokada możliwości edycji plików poprzez panel administracyjny
+ `BASIC_MODULES` — lista podstawowych modułów, których nie można usunąć
+ `HTML_BEAUTY` — ładne formatowanie kodu HTML po jego przeparsowaniu
+ `DEV_MODE` — tryb dewelopera, podczas którego pokazywane są błędy i notatki PHP


Aktualizacja
------------

Jeżeli chcesz być na bieżąco wraz ze wszelkimi nowościami oraz poprawkami błędów i zabezpieczeń, powinieneś regularnie sprawdzać aktualizacje Batflata. Możesz to zrobić w zakładce `Ustawienia -> Aktualizacje`. System sprawdzi czy dostępna jest nowa wersja skryptu oraz automatycznie pobierze z naszego serwera nową paczkę i zaktualizuje pliki rdzenia oraz modułów.

W przypadku komplikacji *(np. niepowodzenia pobrania nowej paczki)* możesz skorzystać z trybu manualnego. W tym celu należy ręcznie pobrać najnowszą wersję Batflata, wgrać ją do głównego katalogu aplikacji, a następnie na końcu adresu URL zakładki z aktualizacją dodać parametr `&manual`. CMS powinien wykryć wgraną paczkę ZIP i po kliknięciu w przycisk aktualizacji, przeprowadzić proces wypakowania i nadpisania plików.

Przed każdą aktualizacją, Batflat wykonuje kopię zapasową. Znajdziesz ją w katalogu skryptu, w folderze `backup/`. W razie niepowodzenia przeprowadzonej aktualizacji, możesz w każdej chwili ją przywrócić.


Motywy
======

Struktura
---------

Struktura motywów w Batflacie jest bardzo prosta. Wystarczy utworzyć nowy folder z nazwą motywu w katalogu `themes/` oraz następujące pliki:

+ `index.html` — domyślny szablon dla podstron
+ `manifest.json` — informacje o motywie
+ `preview.png` — zrzut ekranu przedstawiający motyw *(opcjonalne)*

Każda podstrona może korzystać z innego szablonu, zatem prócz wspomnianego pliku możesz utworzyć także inne, np. `xyz.html`. Wybór szablonu jest dostępny w panelu administracyjnym podczas tworzenia strony. Nie ma określonych reguł co do plików CSS oraz JS. Panuje pełna dowolność.

W katalogu motywu możesz również stworzyć własne widoki modułów. Aby to zrobić, musisz utworzyć katalog `modules/nazwa_modułu/` oraz pliki `*.html` z nazwami odpowiadającymi nazwom oryginalnych widoków. Przykładowo, widok modułu formularza kontaktowego powinien być zawarty w następującej ścieżce: `themes/nazwa_motywu/modules/contact/form.html`. Batflat automatycznie wykryje nowy widok i użyje go zamiast domyślnego widoku modułu.


Tagi szablonów
--------------

CMS korzysta z prostego systemu szablonów, który zawiera następujące tagi:

### Zmienne
```php
{$foo}        // zwykła zmienna
{$foo|e}      // HTML escape dla zmiennej
{$foo|cut:10} // zawartość zmiennej ucięta do 10 znaków
{$foo.bar}    // tablica
```
Dostęp do elementów tablicy odbywa się poprzez znak kropki.

### Warunki
```php
{if: $foo > 5}
    lorem
{elseif: $foo == 5}
    ipsum
{else}
    dolor
{/if}
```

### Pętle
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
Tag pętli posiada 3 stopnie rozbudowania. W pierwszym wystarczy zmienna tablicowa, którą system szablonów rozbije na trzy zmienne o nazwach `$key`, `$value` oraz `$counter`,
która zlicza kolejne iteracje począwszy od zera. Drugi stopień pozwala określić nazwę zmiennej przechowującą wartość, zaś trzeci również nazwę zmiennej indeksu.

### Dołączanie plików szablonu
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

### Kod PHP
```php
Dzisiejsza data: {?= date('Y-m-d') ?}
```
Jeżeli opuścisz znak `=`, kod jedynie się wykona i nic nie wyświetli. Dzięki temu można, np. zdefiniować nowe zmienne wewnątrz szablonu:
```php
{? $foo = 5 ?}
```

### Wyłączenie parsowania
```
{noparse}Użyj tagu {$contact.form}, aby wyświetlić formularz kontaktowy.{/noparse}
```
Wszelkie tagi wewnątrz wyrażenia *noparse* pozostaną w niezmienionym stanie.

### Komentarze
```
{* to jest komentarz *}
```
Komentarze nie są widoczne w pliku źródłowym po skompilowaniu szablonu.

### Języki
```
{lang: pl_polski}
    Witaj świecie!
{/lang}
{lang: en_english}
    Hello world!
{/lang}
```
Jeżeli chcesz dostosować elementy szablonu do konkretnego języka, użyj powyższych tagów.



Zmienne systemowe
-----------------
Batflat, podobnie jak i jego moduły, dostarcza wiele zmiennych *(zazwyczaj tablicowych)*, które służą do wyświetlania poszczególnych elementów strony. Oto najważniejsze z nich:

+ `{$settings.pole}` — element tablicy zawierający wartość danego pola ustawień Batflata
+ `{$settings.moduł.pole}` — element tablicy zawierający wartość danego pola ustawień modułu
+ `{$bat.path}` — przechowuje ścieżkę, w której znajduje się system
+ `{$bat.lang}` — wyświetla aktualnie wykorzystywany język
+ `{$bat.notify}` — ostatnie powiadomienie
+ `{$bat.notify.text}` - tekst powiadomienia
+ `{$bat.notify.type}` - typ komunikatu odpowiadający klasom bootstrapa (danger, success)
+ `{$bat.header}` — dodatkowe meta-tagi, skrypty JS i arkusze stylów CSS ładowane przez moduły
+ `{$bat.footer}` — dodatkowe skrypty JS ładowane przez moduły
+ `{$bat.theme}` — wyświetla ścieżkę do aktywnego szablonu strony wraz z hostem
+ `{$bat.powered}` — wyświetla napis *Powered by Batflat* z linkiem do oficjalnej strony
+ `{$navigation.xyz}` — wyświetla listę `<li>` elementów danej nawigacji
+ `{$page.title}` — wyświetla nazwę podstrony
+ `{$page.content}` — wyświetla zawartość podstrony

Przykład
--------

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

Moduły
======

Struktura
---------

Każdy moduł, podobnie jak motywy, musi znajdować się w osobnym folderze utworzonym w ścieżce `inc/modules/`. Należy zwrócić uwagę na to, by katalog nie zawierał wielkich liter oraz znaków specjalnych, np. spacji.

Podczas tworzenia modułu należy zastanowić się nad tym, jakiego typu ma być to moduł. Czy ma być konfiguralny w panelu administracyjnym, czy może ma działać wyłącznie po stronie gości odwiedzających stronę? Ze względu na taki podział, w Batflacie wyróżniamy trzy główne pliki modułu:

+ `Info.php` — zawiera informacje o module, takie jak nazwa, opis, autor czy ikona
+ `Admin.php` — zawartość tego pliku będzie dostępna poprzez panel administracyjny
+ `Site.php` — zawartość tego pliku będzie dostępna dla gości odwiedzających stronę

Czwartym, lecz opcjonalnym plikiem jest `ReadMe.md`, który powinien zawierać dodatkowe informacje dla przyszłego użytkownika zapisane w jęzku [Markdown](https://en.wikipedia.org/wiki/Markdown), np. sposób używania modułu.

Jeżeli planujesz napisać moduł, który będzie używał HTML, to dobrze byłoby zadbać o to, by kod PHP był oddzielony od hipertekstowego języka znaczników. W tym celu, wewnątrz folderu z modułem, musisz utworzyć katalog `views`. W nim składuj wszelkie pliki widokowe.

Podobnie wygląda sprawa z wielojęzycznością modułu. Wystarczy stworzyć pliki językowe z rozszerzeniem `ini` wewnątrz katalogu `lang`.

Struktura modułu powinna wyglądać mniej więcej w taki sposób:
```
example/
|-- lang/
|    |-- admin/
|    |    |-- en_english.ini
|    |    |-- pl_polski.ini
|    |-- en_english.ini
|    |-- pl_polski.ini
|-- views/
|    |-- admin/
|    |    |-- bar.html
|    |-- foo.html
|-- Admin.php
|-- Info.php
|-- Site.php
+-- ReadMe.md
```

Tworzenie modułu
----------------

### Plik Info

Najważniejszy plik każdego modułu. To w nim zawarte są podstawowe informacje oraz polecenia wykonywane podczas instalacji oraz deinstalacji. 

```php
<?php

    return [
        'name'          =>  'Example',                  // Nazwa
        'description'   =>  'Lorem ipsum....',          // Opis
        'author'        =>  'Robin',                    // Autor
        'version'       =>  '1.0',                      // Wersja
        'compatibility' =>  '1.3.*',                    // Kompatybilność z wersją Batflata
        'icon'          =>  'bolt',                     // Ikona
        
        'pages'         =>  ['Example' => 'example'],   // Rejestracja jako strona (opcjonalne)

        'install'       =>  function() use($core)       // Polecenia instalacji
        {
            // lorem ipsum...
        },
        'uninstall'     =>  function() use($core)       // Polecenia deinstalacji
        {
            // lorem ipsum...    
        }
    ];
```

Lista ikon, które możesz użyć w tym pliku, znajduje się na stronie [fontawesome.io](http://fontawesome.io/icons/). Pamiętaj, by nie wprowadzać nazwy ikony z przedrostkiem `fa-`.

Rejestracja modułu jako strony pozwala na swobodniejsze stosowanie routingu oraz wybranie go jako stronę startową.


### Plik Admin

Zawartość tego pliku będzie uruchomiona w panelu administracyjnym.

```php
<?php
    namespace Inc\Modules\Example;

    use Inc\Core\AdminModule;

    class Admin extends AdminModule
    {
        public function init()
        {
            // Procedury wywoływane przy inicjalizacji modułu
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

W metodzie `navigation` należy zawrzeć tablicę z podstronami modułu. Do każdej z podstron powinna być przypisana jakaś metoda *(bez prefiksu)*. Elementy tej tablicy zostaną wyświetlone w menu panelu administracyjnego.

Metody mogą przyjmować również argumenty, które są przekazywane poprzez adres URL. Przykładowo, po wprowadzeniu adresu `/example/foo/abc`, metoda `getFoo` zwróci tekst *"Foo abc!"*.

Jak można zauważyć w powyższym listingu, każda metoda reprezentująca podstronę modułu powinna być opatrzona prefiksem określającym typ żądania. W większości przypadków będziemy stosować nazewnictwo `getFoo`, zaś dla przesyłanych formularzy `postFoo`. Jeżeli metoda ma obsługiwać wszystkie typy, to powinna być poprzedzona prefiksem `any` *(przykładowo: `anyFoo`)*s. Jest to ważne, ponieważ bez prefixu strony nie będą obsłużone. Obsłużone metody są tłumaczone przez dynamiczny routing w następujący sposób:

+ `getFoo()` — jako `/example/foo` dla żądania typu GET
+ `getFoo($parm)` — jako `/example/foo/abc` dla żądania typu GET
+ `postBar()` — jako `example/bar` dla żądania typu POST *(przesyłanie formularzy)*
+ `anyFoo()` — jako `/example/foo` dla każdego rodzaju żądania

### Plik Site

Plik ten odpowiada za część widzianą przez gości odwiedzających stronę. Jeżeli moduł jest dość spory, dobrą praktyką jest zarejestrowanie go jako strony i zastosowanie routingu.

```php
<?php

    namespace Inc\Modules\Example;
    
    use Inc\Core\SiteModule

    class Site extends SiteModule
    {
        public function init()
        {
            $this->_foo();
        }

        public function routes()
        {
            $this->route('example', 'mySite');
        }

        public function mySite()
        {
            $page = [
                'title' => 'Sample title..',
                'desc' => 'Site description',
                'content' => 'Lorem ipsum dolor...'
            ];

            $this->setTemplate('index.html');
            $this->tpl->set('page', $page);
        }

        private function _foo()
        {            
            $this->tpl->set('bar', 'Why So Serious?');
        }
    }
```

W powyższym przykładzie została utworzona nowa zmienna szablonowa `bar`, która poprzez wywołanie metody `_foo()` w inicjalizatorze modułu, będzie mogła być użyta w plikach motywu jako `{$bar}`. Dodatkowo w metodzie `routes()` został utworzony routing do podstrony `/example`, wskazujący wywołanie metody `mySite()`. Jeżeli przejdziemy pod adres `http://example.com/example` zostanie wywołana metoda `mySite()` modułu.

### Pliki językowe

Moduł może zawierać zmienne językowe, z których można korzystać zarówno w klasach, jak i widokach. Pliki językowe posiadają rozszerzenie `.ini`, a ich miejsce znajduje się w katalogu `lang` danego modułu.
Przykładowo, jeżeli chcesz dodać plik językowy zawierający angielskie wyrażenia dla części administracyjnej modułu `Example`, pownieneś utworzyć nowy plik w ścieżce `inc/modules/example/lang/admin/en_english.ini`.
Zawartość powinna przypominać poniższy listing:

```
full_name           = "Firstname and surname"
email               = "E-mail"
subject             = "Subject"
message             = "Message"
send                = "Send"
send_success        = "Mail successfully sent. I will contact you soon."
send_failure        = "Unable to send a message. Probably mail() function is disabled on the server."
wrong_email         = "Submited e-mail address is incorrect."
empty_inputs        = "Fill all required fields to send a message."
antiflood           = "You have to wait a while before you will send another message."
```

Aby skorzystać z utworzonych zmiennych w klasie modułu należy użyć konstrukcji `$this->lang('subject')`, zaś w widoku `{$lang.example.subject}`. W przypadku klasy możemy opuścić drugi parametr metody `lang` jakim jest nazwa modułu, z którego będzie pobierane tłumaczenie.


Routing
-------

Routing to proces przetwarzania otrzymanego adresu żądania i na jego podstawie decydowanie, co powinno zostać uruchomione lub wyświetlone. Ma on za zadanie wywołać odpowiednią metodę/funkcję na podstawie adresu URL strony. Z routingu należy korzystać wewnątrz publicznej metody `routes()`.

```php
void route(string $pattern, mixed $callback)
```

Pierwszym parametrem metody `route` jest wyrażenie regularne. Niektóre z wyrażeń zostały już zdefiniowane:

+ `:any` — dowolny ciąg znaków
+ `:int` — liczby całkowite
+ `:str` — ciąg znaków będący slugiem

Drugim parametrem jest nazwa metody lub anonimowa funkcja, która przekazuje dowolną liczbę argumentów zdefiniowanych w wyrażeniu regularnym.

#### Przykład
```php
public function routes()
{
    // URL: http://example.com/blog

    // - poprzez wywołanie metody wewnątrz modułu:
    $this->route('blog', 'importAllPosts');

    // - poprzez wywołanie anonimowej funkcji:
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


Metody
------

Moduły posiadają specjalne fasady, które ułatwiają dostęp do metod wewnątrz rdzenia. Dzięki temu można skrócić wywołania typu `$this->core->foo->bar`.

### db

```php
void db([string $table])
```

Umożliwia operowanie na bazie danych. Szczegóły zostały opisane w sekcji rdzenia.

#### Argumenty
+ `table` — nazwa tabeli bazy danych *(opcjonalne)*

#### Przykład
```php
$this->db('table')->where('age', 20)->delete();
```


### draw

```php
string draw(string $file [, array $variables])
```

Zwraca skompilowany kod widoku, w którym wcześniej zostały użyte tagi systemu szablonów. Pozwala również na zdefiniowanie zmiennych zastępując metodę `set()`.

#### Argumenty
+ `file` — nazwa pliku z widokiem wewnątrz modułu lub ścieżka do pliku znajdującego się poza nim
+ `variables` — tablica z definicją zmiennych, które będzie można wykorzystać jako tagi *(opcjonalne)*

#### Przykład
```php
// Kompilacja widoku znajdującego się wewnątrz modułu
$this->draw('form.html', ['form' => $this->formFields]);

// Kompilacja widoku znajdującego się poza modułem
$this->draw('../path/to/view.html', ['foo' => 'bar']);
```


### lang

```php
string lang(string $key [, string $module])
```

Zwraca zawartość klucza tablicy językowej z aktualnego modułu bądź wskazanego poprzez drugi argument.

#### Argumenty
+ `key` — nazwa klucza tablicy językowej
+ `module` — nazwa modułu, z którego ma zostać pobrany klucz *(opcjonalne)*

#### Przykład
```php
// Odwołanie się do tłumaczenia lokalnego
$this->lang('foo');                 // $this->core->lang['module-name']['foo'];

// Odwołanie się do ogólnego tłumaczenia
$this->lang('cancel', 'general');   // $this->core->lang['general']['cancel'];

// Odwołanie się do tłumaczenia modułu "pages"
$this->lang('slug', 'pages')        // $this->core->lang['pages']['slug'];
```


### notify

```php
void notify(string $type, string $text [, mixed $args [, mixed $... ]])
```

Umożliwia wywołanie notyfikacji dla użytkownika.

#### Argumenty
+ `type` — rodzaj powiadomienia: *success* lub *failure*
+ `text` — treść powiadomienia
+ `args` — dodatkowe argumenty *(opcjonalne)*

#### Przykład
```php
$foo = 'Bar';
$this->notify('success', 'This is %s!', $foo); // $this->core->setNotify('success', 'This is %s!', $foo);

```


### settings

```php
mixed settings(string $module [, string $field [, string $value]])
```

Pobiera lub ustawia wartość ustawień danego modułu.

#### Argumenty
+ `module` — nazwa modułu i opcjonalnie nazwa pola oddzielona kropką
+ `field` — nazwa pola modułu *(opcjonalne)*
+ `value` — wartość na jaką zostanie zmienione pole modułu *(opcjonalne)*

#### Przykład
```php
// Pobranie pola "desc" z modułu "blog"
$this->settings('blog.desc');    // $this->core->getSettings('blog', 'desc');

// Pobranie pola "desc" z modułu "blog"
$this->settings('blog', 'desc'); // $this->core->getSettings('blog', 'desc');

// Ustawienie zawartości pola "desc" z modułu "blog"
$this->settings('blog', 'desc', 'Lorem ipsum...');
```

### setTemplate

```php
void setTemplate(string $file)
```

Umożliwia podmianę pliku z szablonem na froncie. Metoda działa wyłącznie w klasie `Site`. 

#### Argumenty
+ `file` — nazwa pliku z szablonem

#### Przykład
```php
$this->setTemplate('index.html'); // $this->core->template = 'index.html';
```


Rdzeń
=====

Jest to swoiste jądro / silnik Batflata, czyli najważniejsza część, która jest odpowiedzialna za wszystkie jego podstawowe zadania. Rdzeń zawiera wiele definicji stałych, funkcji i metod, które możesz wykorzystać podczas pisania modułów. 

Stałe
-----
Wszelkie definicje stałych zostały opisane w pierwszej części tej dokumentacji. Aby z nich skorzystać, wystarczy w pliku PHP wywołać ich nazwy. Stałe szczególnie przydają się przy budowie adresów URL i ścieżek do plików.

#### Przykład
```php
echo MODULES.'/contact/view/form.html';

```


Funkcje
-------

Batflat posiada kilka wbudowanych funkcji pomocniczych, które ułatwiają tworzenie modułów.

### domain

```php
string domain([bool $with_protocol = true])
```

Zwraca nazwę domeny z protokołem http(s) lub bez.

#### Argumenty
+ `with_protocol` — decyduje czy adres zostanie zwrócony z protokołem czy bez

#### Wartość zwracana
Tekst z nazwą domeny.

#### Przykład
```php
echo domain(false);
// Wynik: example.com
```


### checkEmptyFields

```php
bool checkEmptyFields(array $keys, array $array)
```

Sprawdza czy tablica zawiera puste elementy. Przydaje się podczas walidacji formularzy. 

#### Argumenty
+ `keys` — lista elementów tablicy, które funkcja ma sprawdzić
+ `array` — tablica źródłowa

#### Wartość zwracana
Zwraca `TRUE`, gdy przynajmniej jeden element jest pusty. `FALSE`, gdy wszystkie elementy są uzupełnione.

#### Przykład
```php
if(checkEmptyFields(['name', 'phone', 'email'], $_POST) {
    echo 'Wypełnij wszystkie pola!';
}
```


### currentURL

```php
string currentURL([bool $query = false])
```

Zwraca aktualny adres URL.

#### Argumenty
+ `query` — decyduje czy adres zostanie zwrócony ze zmiennymi czy bez

#### Przykład
```php
echo currentURL();
// Wynik: http://example.com/contact

echo currentURL(true);
// Wynik: http://example.com/contact?foo=bar
```


### createSlug

```php
string createSlug(string $text)
```

Zamienia w tekście znaki językowe na bez ogonków, spacje na myślniki oraz usuwa znaki specjalne. Służy do tworzenia slugów w adresach URL i nazw zmiennych w systemie szablonów.

#### Argumenty
+ `text` — tekst do konwersji

#### Wartość zwracana
Zwraca tekst w postaci slugu.

#### Przykład
```php
echo createSlug('Być albo nie być; oto jest pytanie!');
// Wynik: byc-albo-nie-byc-oto-jest-pytanie
```


### deleteDir

```php
bool deleteDir(string $path)
```

Rekurencyjna funkcja, która usuwa katalog wraz z całą zawartością.

#### Argumenty
+ `path` — ścieżka do katalogu

#### Wartość zwracana
Zwraca `TRUE` w przypadku sukcesu lub `FALSE` przy niepowodzeniu.

#### Przykład
```php
deleteDir('foo/bar');
```


### getRedirectData
```php
mixed getRedirectData()
```

Zwraca dane przekazane do sesji podczas użycia funkcji `redirect()`.

#### Wartość zwracana
Tablica lub `null`.

#### Przykład
```php
$postData = getRedirectData();
```


### htmlspecialchars_array

```php
string htmlspecialchars_array(array $array)
```

Zamienia znaki specjalne z elementów tablicy na encje HTML.

#### Argumenty
+ `array` — tablica, która zostanie przekonwertowana

#### Wartość zwracana
Zwraca skonwertowany tekst.

#### Przykład
```php
$_POST = htmlspecialchars_array($_POST);
```


### isset_or

```php
mixed isset_or(mixed $var [, mixed $alternate = null ])
```

Zamienia pustą zmienną na wartość alternatywną.

#### Argumenty
+ `var` — zmienna
+ `alternate` — wartość zastępcza zmiennej *(opcjonalne)*

#### Wartość zwracana
Zwraca wartość alternatywną.

#### Przykład
```php
$foo = isset_or($_GET['bar'], 'baz');
```


### parseURL
```php
mixed parseURL([ int $key = null ])
```

Parsuje aktualny adresu URL skryptu.

#### Argumenty
+ `key` — numer parametru adresu URL *(opcjonalne)*

#### Wartość zwracana
Tablica lub jej pojedynczy element.

#### Przykład
```php
// URL: http://example.com/foo/bar/4

var_dump(parseURL())
// Wynik:
// array(3) {
//   [0] =>
//   string(3) "foo"
//   [1] =>
//   string(3) "bar"
//   [2] =>
//   int(4)
// }

echo parseURL(2);
// Wynik: "bar"
```


### redirect

```php
void redirect(string $url [, array $data = [] ])
```

Przekierowanie do wskazanego adresu URL. Pozwala ona na zapisanie danych z tablicy do sesji. Przydaje się do zapamiętywania niezapisanych danych z formularzy.

#### Argumenty
+ `url` — adres, na który nastąpi przekierowanie
+ `data` — tablica, która będzie przekazana do sesji *(opcjonalne)*

#### Przykład
```php
redirect('http://www.example.com/');

// Zapisanie tablicy do sesji:
redirect('http://www.example.com/', $_POST);
```


### url
```php
string url([ mixed $data = null ])
```

Tworzy bezwzględny adres URL. W panelu administracyjnym automatycznie dodaje token.

#### Argumenty
+ `data` — tekst lub tablica

#### Wartość zwracana
Bezwzględny adres URL.

#### Przykład
```php
echo url();
// Wynik: http://example.com

echo url('foo/bar')
// Wynik: http://example.com/foo/bar

echo url('admin/foo/bar');
// Wynik: http://example.com/admin/foo/bar?t=[token]

echo url(['admin', 'foo', 'bar']);
// Wynik: http://example.com/admin/foo/bar?t=[token]
```


Metody
------

Prócz funkcji, istnieje kilka istotnych metod, które przyspieszają proces tworzenia nowych funkcjonalności systemu.

### addCSS

```php
void addCSS(string $path)
```

Importuje plik CSS w nagłówku motywu.

#### Argumenty
+ `path` — adres URL do pliku

#### Przykład
```php
$this->core->addCSS('http://example.com/style.css');
// Wynik: <link rel="stylesheet" href="http://example.com/style.css" />
```


### addJS

```php
void addJS(string $path [, string $location = 'header'])
```

Importuje plik JS w nagłówku lub stopce motywu.

#### Argumenty
+ `path` — adres URL do pliku
+ `location` — lokalizacja: *header* lub *footer* *(opcjonalne)*

#### Przykład
```php
$this->core->addJS('http://example.com/script.js');
// Wynik: <script src="http://example.com/script.js"></script>
```


### append

```php
void append(string $string, string $location)
```

Dodaje do nagłówka lub stopki ciąg znaków.

#### Argumenty
+ `string` — ciąg znaków
+ `location` — lokalizacja: *header* lub *footer*

#### Przykład
```php
$this->core->append('<meta name="author" content="Bruce Wayne">', 'header');
```


### getModuleInfo

```php
array getModuleInfo(string $dir)
```

Zwraca informacje o module. Metoda działa wyłącznie w klasie `Admin`. 

#### Argumenty
+ `name` — nazwa katalogu modułu

#### Wartość zwracana
Tablica z informacjami.

#### Przykład
```php
$foo = $this->core->getModuleInfo('contact');
```


### getSettings

```php
mixed getSettings([string $module = 'settings', string $field = null])
```

Pobiera wartość ustawień danego modułu. Domyślnie są to główne ustawienia Batflata.

#### Argumenty
+ `module` — nazwa modułu *(opcjonalne)*
+ `field` — pole z definicją ustawienia *(opcjonalne)*

#### Wartość zwracana
Tablica lub tekst.

#### Przykład
```php
echo $this->core->getSettings('blog', 'title');
```


### getUserInfo

```php
string getUserInfo(string $field [, int $id ])
```

Zwraca informacje o zalogowanym użytkowniku lub o danym ID. Metoda działa wyłącznie w klasie `Admin`.

#### Argumenty
+ `field` — nazwa pola w bazie danych
+ `id` — numer identyfikacyjny *(opcjonalne)*

#### Wartość zwracana
Ciąg znaków z wybranego pola.

#### Przykład
```php
// Aktualnie zalogowany użytkownik
$foo = $this->core->getUserInfo('username');

// Użytkownik o danym ID
$foo = $this->core->getUserInfo('username', 1);
```


### setNotify

```php
void setNotify(string $type, string $text [, mixed $args [, mixed $... ]])
```

Generuje notyfikację.

#### Argumenty
+ `type` — rodzaj powiadomienia: *success* lub *failure*
+ `text` — treść powiadomienia
+ `args` — dodatkowe argumenty *(opcjonalne)*

#### Przykład
```php
$foo = 'Bar';
$this->core->setNotify('success', 'Pomyślnie zainstalowano moduł %s!', $foo);
// Wynik: "Pomyślnie zainstalowano moduł Bar!"
```


Baza danych
-----------

Zastosowana w Batflacie baza danych to SQLite w wersji 3. Do jej obsługi CMS wykorzystuje prostą klasę, która ułatwia budowanie zapytań. Nie musisz znać języka SQL, by móc na niej operować.

Dodatkowo polecamy narzędzie [phpLiteAdmin](https://github.com/sruupl/batflat-pla) do zarządzania bazą. Jest to jednoplikowy skrypt PHP, podobny do *phpMyAdmin*, przy pomocy którego można administrować tabelami Batflata. Pozwoli on zapoznać się ze strukturą istniejących tabel.
Plik bazy znajduje się w `inc/data/database.sdb`.


### SELECT

Pobieranie wielu rekordów:

```php
// JSON
$rows = $this->core->db('table')->toJson();

// Tablica
$rows = $this->core->db('table')->select('foo')->select('bar')->toArray();

// Obiekt
$rows = $this->core->db('table')->select(['foo', 'b' => 'bar'])->toObject();
```

Pobieranie pojedycznego rekordu:
```php
// JSON
$row = $this->core->db('table')->oneJson();

// Tablica
$row = $this->core->db('table')->select('foo')->select('bar')->oneArray();

// Obiekt
$row = $this->core->db('table')->select(['foo', 'b' => 'bar'])->oneObject();
```


### WHERE

Wybór wiersza o określonym numerze w kolumnie `id`:

```php
$row = $this->core->db('table')->oneArray(1);
// lub
$row = $this->core->db('table')->oneArray('id', 1);
// lub
$row = $this->core->db('table')->where(1)->oneArray();
// lub
$row = $this->core->db('table')->where('id', 1)->oneArray();
```

Złożone warunki:
```php
// Pobranie wierszy, których wartość kolumny 'foo' jest WIĘKSZA od 4
$rows = $this->core->db('table')->where('foo', '>', 4)->toArray();

// Pobranie wierszy, których wartość kolumny 'foo' jest WIĘKSZA od 4 i MNIEJSZA od 8
$rows = $this->core->db('table')->where('foo', '>', 4)->where('foo', '<', 8)->toArray();
```

OR WHERE:
```php
// Pobranie wierszy, których wartość kolumny 'foo' jest RÓWNA 4 LUB 8
$rows = $this->core->db('table')->where('foo', '=', 4)->orWhere('foo', '=', 8)->toArray();
```

WHERE LIKE:
```php
// Pobranie wierszy, których kolumna 'foo' ZAWIERA ciąg znaków 'bar' LUB 'baz'
$rows = $this->core->db('table')->like('foo', '%bar%')->orLike('foo', '%baz%')->toArray();
```

WHERE NOT LIKE:
```php
// Pobranie wierszy, których kolumna 'foo' NIE ZAWIERA ciągu znaków 'bar' LUB 'baz'
$rows = $this->core->db('table')->notLike('foo', '%bar%')->orNotLike('foo', '%baz%')->toArray();
```

WHERE IN:
```php
// Pobranie wierszy, których wartość kolumny 'foo' ZAWIERA SIĘ w tablicy [1,2,3] LUB [7,8,9]
$rows = $this->core->db('table')->in('foo', [1,2,3])->orIn('foo', [7,8,9])->toArray();
```

WHERE NOT IN:
```php
// Pobranie wierszy, których wartość kolumny 'foo' NIE ZAWIERA SIĘ w tablicy [1,2,3] LUB [7,8,9]
$rows = $this->core->db('table')->notIn('foo', [1,2,3])->orNotIn('foo', [7,8,9])->toArray();
```

Grupowanie warunków:
```php
// Pobranie wierszy, których wartość kolumny 'foo' to 1 lub 2 ORAZ posiadają status = 1
$rows = $this->core->db('table')->where(function($st) {
            $st->where('foo', 1)->orWhere('foo', 2);
        })->where('status', 1)->toArray();
```

Dozwolone operatory porównania: `=`, `>`, `<`, `>=`, `<=`, `<>`, `!=`. 


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

Metoda `save` może dodać nowy rekord do tabeli lub zaktualizować istniejące, gdy zostanie opatrzona warunkiem. Po dodaniu nowego rekordu zostanie zwrócony jego numer identyfikacyjny.

```php
// Dodanie nowego rekordu
$id = $this->core->db('table')->save(['name' => 'James Gordon', 'city' => 'Gotham']);
// Wartość zwracana: numer ID nowego rekordu

// Aktualizacja istniejącego rekordu
$this->core->db('table')->where('age', 50)->save(['name' => 'James Gordon', 'city' => 'Gotham']);
// Wartość zwracana: TRUE w przypadku sukcesu lub FALSE przy niepowodzeniu
```


### UPDATE

Aktualizacja rekordów w przypadku powodzenia zwróci wartość `TRUE`. W przeciwnym razie będzie to `FALSE`.

```php
// Zmiana jednej kolumny
$this->core->db('table')->where('city', 'Gotham')->update('name', 'Joker');

// Zmiana wielu kolumn
$this->core->db('table')->where('city', 'Gotham')->update(['name' => 'Joker', 'type' => 'Villain']);
```


### SET

```php
$this->core->db('table')->where('age', 65)->set('age', 70)->set('name', 'Alfred Pennyworth')->update();
```


### DELETE

Pomyślne usunięcie rekordów zwróci ich liczbę.

```php
// Usunięcie rekordu o `id` równym 1
$this->core->db('table')->delete(1);

// Usunięcie rekordu z warunkiem
$this->core->db('table')->where('age', 20)->delete();
```


### ORDER BY

Sortowanie rosnące:
```php
$this->core->db('table')->asc('created_at')->toJson();
```

Sortowanie malejące:
```php
$this->core->db('table')->desc('created_at')->toJson();
```

Łączenie sortowania:
```php
$this->core->db('table')->desc('created_at')->asc('id')->toJson();
```


### GROUP BY

```php
$this->core->db('table')->group('city')->toArray();
```


### OFFSET, LIMIT

```php
// Pobranie 5 rekordów, zaczynając od dziesiątego
$this->core->db('table')->offset(10)->limit(5)->toJson();
```


### PDO

Nie wszystkie zapytania da się stworzyć przy pomocy wyżej wymienionych metod *(np. utworzenie lub usunięcie tabeli)*, dlatego możesz również pisać zapytania przy pomocy [PDO](http://php.net/manual/en/book.pdo.php):

```php
$this->core->db()->pdo()->exec("DROP TABLE `example`");
``` 


System szablonów
----------------

Obsługa systemu szablonów jest łatwa i opiera się głównie na dwóch metodach. Jedna pozwala na przypisanie zmiennych, natomiast druga zwraca skompilowany kod. W wyjątkowych sytuacjach przydają się pozostałe dwie metody.

### set

```php
void set(string $name, mixed $value)
```

Przypisuje wartość lub funkcję do zmiennej, którą będzie można się posługiwać w widokach.

#### Argumenty
+ `name` — nazwa zmiennej
+ `value` — wartość zmiennej lub anonimowa funkcja

#### Przykład
```php
// Przypisanie tablicy
$foo = ['bar', 'baz', 'qux'];
$this->tpl->set('foo', $foo);

// Przypisanie funkcji anonimowej
$this->tpl->set('bar', function() {
   return ['baz' => 'qux']; 
})
```


### draw

```php
string draw(string $file)
```

Zwraca skompilowany kod widoku, w którym wcześniej zostały użyte tagi systemu szablonów.

#### Argumenty
+ `file` — ścieżka do pliku

#### Wartość zwracana
Ciąg znaków, tj. skompilowany widok.

#### Przykład
```php
$this->tpl->draw(MODULES.'/galleries/view/admin/manage.html');
```


### noParse

```php
string noParse(string $text)
```

Chroni przed kompilacją tagów systemu szablonów.

#### Argumenty
+ `text` — tekst, który ma być pozostawiony bez zmian

#### Przykład
```php
$this->tpl->noParse('Place this tag in website template: {$contact.form}');
```


### noParse_array

```php
array noParse_array(array $array)
```

Chroni przed kompilacją tagów systemu szablonów wewnątrz tablicy.

#### Argumenty
+ `array` — tablica, która ma być pozostawiona bez zmian

#### Przykład
```php
$this->tpl->noParse_array(['{$no}', '{$changes}']);
```

Języki
------

Wszelkie pliki językowe znajdują się w katalogach `lang` wewnątrz modułów oraz pod ścieżką `inc/lang`.
W tej ostatniej ścieżce istnieją foldery odpowiadające nazwom języków w następującym formacie: `en_english`. Pierwszy człon to skrót języka, a drugi to jego pełna nazwa.
Wewnątrz katalogu mieści się plik `general.ini`, w którym zawarte są ogólne zmienne językowe dla systemu.
Po utworzeniu nowego folderu językowego, Batflat automatycznie wykryje dodany język i umożliwi jego wybranie w panelu administracyjnym. Należy jednak pamiętać, że procedurę stworzenia nowego języka należy powtórzyć dla każdego modułu.
