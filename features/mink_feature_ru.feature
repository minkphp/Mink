# language: ru
Функционал: Шаги Mink
  Для того, чтобы описывать поведение веб-приложений
  Как веб-девелопер
  Мне нужна возможность говорить с Mink через Behat

  @mink:zombie
  Сценарий: Стандартная форма (через Sahi)
    Допустим я на странице "basic_form.php"
    Когда я ввожу "Konstantin" в поле "first_name"
    И ввожу "Kudryashov" в поле "lastn"
    И я нажимаю "Save"
    Тогда я должен видеть "Anket for Konstantin"
    И я должен видеть "Lastname: Kudryashov"

  Сценарий: Стандартная форма (через Goutte)
    Допустим я на странице "basic_form.php"
    Когда я заполняю поле "first_name" значением "Konstantin"
    И заполняю поле "lastn" значением "Kudryashov"
    И нажимаю "Save"
    Тогда я должен видеть "Anket for Konstantin"
    И я должен видеть "Lastname: Kudryashov"
