<?php

namespace waitingforlove24\Prime\View;

use function line;
use function prompt;

function showMessage(string $message): void {
    // Выводит сообщение в консоль
    line($message);
}

function askQuestion(string $question): string {
    // Задает вопрос пользователю и получает ответ
    return prompt($question);
}

function displayResult(int $number, bool $isPrime, array $divisors = []): void {
    if ($isPrime) {
        line("Число {$number} является простым.");
    } else {
        line("Число {$number} не является простым.");
        if (!empty($divisors)) {
            line("Его нетривиальные делители: " . implode(', ', $divisors));
        }
    }
}