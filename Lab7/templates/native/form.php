<form method="POST" action="index.php?engine=native">
    <div class="form-group">
        <label for="name">Название привычки (string)</label>
        <input type="text" id="name" name="name" required minlength="3" placeholder="Например: Читать 2 страницы в день">
    </div>

    <div class="form-group">
        <label for="description">Описание / Мотивация (text)</label>
        <textarea id="description" name="description" required placeholder="Зачем мне это нужно?"></textarea>
    </div>

    <div class="form-group">
        <label for="start_date">Дата начала (date)</label>
        <input type="date" id="start_date" name="start_date" required>
    </div>

    <div class="form-group">
        <label for="category">Категория (enum)</label>
        <select id="category" name="category" required>
            <option value="Здоровье">Здоровье</option>
            <option value="Обучение">Обучение</option>
            <option value="Спорт">Спорт</option>
            <option value="Финансы">Финансы</option>
        </select>
    </div>

    <div class="form-group checkbox-group">
        <input type="checkbox" id="is_daily" name="is_daily" value="1">
        <label for="is_daily" style="margin: 0;">Выполнять ежедневно? (checkbox)</label>
    </div>

    <button type="submit">Сохранить привычку</button>
</form>
