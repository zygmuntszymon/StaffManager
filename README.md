# 👥 StaffManager - System Zarządzania Personelem

## 👨💻 Autorzy  
**Szymon Zygmunt**  
**Sebastian Kowalewski**

## 🛠 Technologie  
- PHP 8 + MVC  
- JavaScript  
- CSS3  
- MySQL (XAMPP)

## 🚀 Funkcje Systemu
- ✔️ Rejestracja i logowanie pracowników
- ✔️ System ról (pracodawca/pracownik)
- ✔️ Przydzielanie i śledzenie zadań
- ✔️ Zarządzanie urlopami i dniami wolnymi
- ✔️ System punktowy za zadania


## 📸 Podgląd  
![Dashboard](/screenshots/1.png)  
![Zadania](/screenshots/2.png)  

## 🔧 Instalacja

1. **Pobierz i zainstaluj XAMPP**  
   [https://www.apachefriends.org/pl/index.html](https://www.apachefriends.org/pl/index.html)

2. **Sklonuj repozytorium**  
   ```bash
   git clone https://github.com/zygmuntszymon/StaffManager.git
   ```
3. **Przeniesienie projektu do XAMPP**  
   ```bash
   # Windows
   xcopy StaffManager C:\xampp\htdocs\ /E /H /C /I

   # Linux/Mac
   sudo cp -r StaffManager /opt/lampp/htdocs/

4. **Konfiguracja bazy danych**  
   ```bash
   # Linux/Mac (przez terminal)
   mysql -u root -p -e "CREATE DATABASE staffmanager_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
   mysql -u root -p staffmanager_db < staffmanager_db.sql

   # Windows (przez phpMyAdmin):
   1. Otwórz http://localhost/phpmyadmin
   2. Kliknij "Nowa baza danych"
   3. Wpisz nazwę: staffmanager_db
   4. Wybierz collation: utf8mb4_general_ci
   5. Zaimportuj plik staffmanager_db.sql

5. **Konfiguracja połączenia PHP-MySQL**  
   Edytuj plik `app/utils/config.php`:  
   ```php
   <?php
   $host = "localhost";     // nie zmieniaj
   $dbname = "staffmanager_db";  // nazwa twojej bazy
   $user = "root";          // domyślny login XAMPP
   $password = "";          // puste hasło domyślnie
   ?>   

6. **Uruchomienie systemu**  
   W przeglądarce odwiedź adres: http://localhost/StaffManager

7. **Pierwsze kroki**  
   a. Przejdź do formularza rejestracji pod adresem:  
      ```
      http://localhost/StaffManager/register
      ```  
   b. Wypełnij formularz rejestracji:  
      ```
      - Imię i nazwisko
      - Pesel
      - Hasło (min. 8 znaków)
      - Wybierz rolę "Pracodawca"
      ```  
   c. Zaloguj się używając utworzonych danych:  
      ```
      http://localhost/StaffManager/login
      ```  
   d. Po zalogowaniu możesz:  
      ```bash
      - Dodawać pracowników
      - Tworzyć zadania
      - Zarządzać urlopami
      - Generować raporty 
      ```
8. **Rozwiązywanie problemów**  
Jeśli wystąpią błędy:  
- Sprawdź czy Apache i MySQL są uruchomione w XAMPP  
- Upewnij się że plik `staffmanager_db.sql` został zaimportowany  
- Zweryfikuj dane w `app/utils/config.php`

## 🗃 Baza Danych  
Plik inicjalizacyjny: `staffmanager_db.sql`
