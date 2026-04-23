<table>
    <thead>
        <tr>
            <th><a href="?engine=native&sort=name">Название ↕</a></th>
            <th>Описание</th>
            <th><a href="?engine=native&sort=category">Категория ↕</a></th>
            <th>Дата старта</th>
            <th>Ежедневно?</th>
            <th><a href="?engine=native&sort=created_at">Добавлено ↕</a></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($habits)): ?>
            <tr><td colspan="6" style="text-align: center;">Список пока пуст. Создайте первую привычку!</td></tr>
        <?php else: ?>
            <?php foreach ($habits as $habit): ?>
                <tr>
                    <td><?= htmlspecialchars($habit['name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($habit['description'])) ?></td>
                    <td>
                        <?php
                            $cat = $habit['category'];
                            $icon = '📌';
                            if ($cat === 'Здоровье') $icon = '🏥';
                            elseif ($cat === 'Обучение') $icon = '📚';
                            elseif ($cat === 'Спорт') $icon = '🏋️';
                            elseif ($cat === 'Финансы') $icon = '💰';
                            echo htmlspecialchars($icon . ' ' . $cat);
                        ?>
                    </td>
                    <td><?= htmlspecialchars($habit['start_date']) ?></td>
                    <td><?= $habit['is_daily'] ? '✅ Да' : '❌ Нет' ?></td>
                    <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($habit['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
