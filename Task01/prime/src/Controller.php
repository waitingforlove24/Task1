<?php

namespace waitingforlove24\Prime;

use function waitingforlove24\Prime\View\showMessage;
use function waitingforlove24\Prime\View\askQuestion;
use function waitingforlove24\Prime\View\displayResult;

class GameController {
    private $db;

    public function __construct() {
        // Подключение к базе данных (MySQL)
        $this->db = new \PDO('mysql:host=localhost;dbname=game_db', 'root', '');
        $this->initDatabase();
    }

    private function initDatabase(): void {
        // Создание таблицы, если она не существует
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS games (
                id INT AUTO_INCREMENT PRIMARY KEY,
                player_name VARCHAR(255) NOT NULL,
                number INT NOT NULL,
                is_prime BOOLEAN NOT NULL,
                divisors TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function startGame(): void {
        // Начало игры
        showMessage("Добро пожаловать в игру 'Простое ли число'!");

        // Спрашиваем имя игрока
        $playerName = askQuestion("Введите ваше имя:");

        // Генерируем случайное число
        $number = rand(1, 100);
        showMessage("Вам предложено число: {$number}");

        // Проверяем, является ли число простым
        $isPrime = $this->isPrime($number);
        $divisors = !$isPrime ? $this->getNonTrivialDivisors($number) : [];

        // Получаем ответ пользователя
        $userAnswer = strtolower(askQuestion("Является ли это число простым? (да/нет):"));

        // Проверяем правильность ответа
        $correctAnswer = $isPrime ? 'да' : 'нет';
        if ($userAnswer === $correctAnswer) {
            showMessage("Правильно! Вы угадали.");
        } else {
            showMessage("Неправильно. Правильный ответ: {$correctAnswer}");
        }

        // Отображаем результат
        displayResult($number, $isPrime, $divisors);

        // Сохраняем результат в базу данных
        $this->saveGameResult($playerName, $number, $isPrime, $divisors);
    }

    private function isPrime(int $number): bool {
        // Проверяет, является ли число простым
        if ($number < 2) {
            return false;
        }
        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i === 0) {
                return false;
            }
        }
        return true;
    }

    private function getNonTrivialDivisors(int $number): array {
        // Находит нетривиальные делители числа
        $divisors = [];
        for ($i = 2; $i <= $number / 2; $i++) {
            if ($number % $i === 0) {
                $divisors[] = $i;
            }
        }
        return $divisors;
    }

    private function saveGameResult(string $playerName, int $number, bool $isPrime, array $divisors): void {
        // Сохраняет результат игры в базу данных
        $stmt = $this->db->prepare("INSERT INTO games (player_name, number, is_prime, divisors) VALUES (?, ?, ?, ?)");
        $stmt->execute([$playerName, $number, $isPrime, implode(',', $divisors)]);
    }
}