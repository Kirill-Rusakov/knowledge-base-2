<?php
require_once 'classes/Database.php';
require_once 'classes/Deal.php';
require_once 'classes/Entity.php'; 
require_once 'classes/Contact.php';

$db = new Database();

$action = $_GET['action'] ?? null;
$entityType = $_GET['entity'] ?? null;
$id = $_GET['id'] ?? null;

if ($action && $entityType && in_array($action, ['edit', 'delete', 'add'])) 
{
    handleAction($action, $entityType, $id, $db);
}

function handleAction($action, $entityType, $id, $db) 
{
    if ($action === 'delete') 
    {
        if ($entityType === 'deal') 
        {
            $db->deleteDeal($id);
        } elseif ($entityType === 'contact') 
        {
            $db->deleteContact($id);
        }
        header('Location: index.php');
        exit;
    } elseif ($action === 'edit' || $action === 'add') 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            if ($entityType === 'deal') 
            {
                $contacts = isset($data['contacts']) ? array_map('intval', $data['contacts']) : [];
                $dealData = [
                    'id' => $action === 'edit' ? (int)$id : null,
                    'name' => trim($data['name']),
                    'amount' => (int)$data['amount'],
                    'contacts' => $contacts
                ];
                
                $db->saveDeal($dealData);
            } elseif ($entityType === 'contact') 
            {
                $deals = isset($data['deals']) ? array_map('intval', $data['deals']) : [];
                $contactData = [
                    'id' => $action === 'edit' ? (int)$id : null,
                    'first_name' => trim($data['first_name']),
                    'last_name' => trim($data['last_name']),
                    'deals' => $deals
                ];
                
                $db->saveContact($contactData);
            }
            
            header('Location: index.php');
            exit;
        }
    }
}

$selectedType = $_GET['type'] ?? 'deal';
$selectedId = $_GET['selected_id'] ?? null;

$deals = $db->getDeals();
$contacts = $db->getContacts();

$dealObjects = [];
foreach ($deals as $deal) 
{
    $dealObjects[] = new Deal($deal['id'], $deal['name'], $deal['amount'], $deal['contacts']);
}

$contactObjects = [];
foreach ($contacts as $contact) 
{
    $contactObjects[] = new Contact($contact['id'], $contact['first_name'], $contact['last_name'], $contact['deals']);
}

$selectedEntity = null;
if ($selectedId) {
    if ($selectedType === 'deal') 
    {
        $deal = $db->getDeal($selectedId);
        if ($deal) 
        {
            $selectedEntity = new Deal($deal['id'], $deal['name'], $deal['amount'], $deal['contacts']);
        }
    } else 
    {
        $contact = $db->getContact($selectedId);
        if ($contact) 
        {
            $selectedEntity = new Contact($contact['id'], $contact['first_name'], $contact['last_name'], $contact['deals']);
        }
    }
}

if (!$selectedEntity && $selectedType === 'deal' && !empty($dealObjects)) 
{
    $selectedEntity = $dealObjects[0];
    $selectedId = $selectedEntity->getId();
} elseif (!$selectedEntity && $selectedType === 'contact' && !empty($contactObjects))
{
    $selectedEntity = $contactObjects[0];
    $selectedId = $selectedEntity->getId();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление сделками и контактами</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Управление сделками и контактами</h1>
        <div class="entity-manager">
            <div class="column menu-column">
                <h2>Меню</h2>
                <ul>
                    <li class="<?= $selectedType === 'deal' ? 'active' : '' ?>">
                        <a href="?type=deal">Сделки</a>
                    </li>
                    <li class="<?= $selectedType === 'contact' ? 'active' : '' ?>">
                        <a href="?type=contact">Контакты</a>
                    </li>
                </ul>
                
                <div class="actions">
                    <a href="?action=add&entity=<?= $selectedType ?>" class="button">Добавить <?= $selectedType === 'deal' ? 'сделку' : 'контакт' ?></a>
                </div>
            </div>
            <div class="column list-column">
                <h2>Список</h2>
                <ul>
                    <?php if ($selectedType === 'deal'): ?>
                        <?php foreach ($dealObjects as $deal): ?>
                            <li class="<?= $selectedId == $deal->getId() ? 'active' : '' ?>">
                                <a href="?type=deal&selected_id=<?= $deal->getId() ?>">
                                    <?= htmlspecialchars($deal->getName()) ?>
                                </a>
                                <div class="item-actions">
                                    <a href="?action=edit&entity=deal&id=<?= $deal->getId() ?>" class="edit">&#9998;</a>
                                    <a href="?action=delete&entity=deal&id=<?= $deal->getId() ?>" class="delete" onclick="return confirm('Удалить сделку?')">&#9746;</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($contactObjects as $contact): ?>
                            <li class="<?= $selectedId == $contact->getId() ? 'active' : '' ?>">
                                <a href="?type=contact&selected_id=<?= $contact->getId() ?>">
                                    <?= htmlspecialchars($contact->getFullName()) ?>
                                </a>
                                <div class="item-actions">
                                    <a href="?action=edit&entity=contact&id=<?= $contact->getId() ?>" class="edit">&#9998;</a>
                                    <a href="?action=delete&entity=contact&id=<?= $contact->getId() ?>" class="delete" onclick="return confirm('Удалить контакт?')">&#9746;</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="column content-column">
                <h2>Содержимое</h2>
                <div class="content">
                    <?php if ($selectedEntity): ?>
                        <?php if ($selectedType === 'deal'): ?>
                            <table>
                                <tr>
                                    <th>Id сделки</th>
                                    <td><?= $selectedEntity->getId() ?></td>
                                </tr>
                                <tr>
                                    <th>Наименование</th>
                                    <td><?= htmlspecialchars($selectedEntity->getName()) ?></td>
                                </tr>
                                <tr>
                                    <th>Сумма</th>
                                    <td><?= number_format($selectedEntity->getAmount(), 0, '', ' ') ?></td>
                                </tr>
                                <?php foreach ($selectedEntity->getContacts() as $contactId): ?>
                                    <?php $contact = $db->getContact($contactId); ?>
                                    <?php if ($contact): ?>
                                        <tr>
                                            <th>Id контакта: <?= $contactId ?></th>
                                            <td><?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <table>
                                <tr>
                                    <th>Id контакта</th>
                                    <td><?= $selectedEntity->getId() ?></td>
                                </tr>
                                <tr>
                                    <th>Имя</th>
                                    <td><?= htmlspecialchars($selectedEntity->getFirstName()) ?></td>
                                </tr>
                                <tr>
                                    <th>Фамилия</th>
                                    <td><?= htmlspecialchars($selectedEntity->getLastName()) ?></td>
                                </tr>
                                <?php foreach ($selectedEntity->getDeals() as $dealId): ?>
                                    <?php $deal = $db->getDeal($dealId); ?>
                                    <?php if ($deal): ?>
                                        <tr>
                                            <th>Id сделки: <?= $dealId ?></th>
                                            <td><?= htmlspecialchars($deal['name']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Нет данных для отображения</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($action === 'edit' || $action === 'add'): ?>
        <div class="modal-overlay">
            <div class="modal">
                <h2><?= $action === 'edit' ? 'Редактирование' : 'Добавление' ?> <?= $entityType === 'deal' ? 'сделки' : 'контакта' ?></h2>
                
                <form method="POST" action="?action=<?= $action ?>&entity=<?= $entityType ?><?= $action === 'edit' ? '&id=' . $id : '' ?>">
                    <?php if ($entityType === 'deal'): ?>
                        <?php $deal = $action === 'edit' ? $db->getDeal($id) : null; ?>
                        <div class="form-group">
                            <label for="name">Наименование*:</label>
                            <input type="text" id="name" name="name" value="<?= $deal ? htmlspecialchars($deal['name']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Сумма:</label>
                            <input type="number" id="amount" name="amount" value="<?= $deal ? $deal['amount'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Контакты:</label>
                            <?php foreach ($contacts as $contact): ?>
                                <div>
                                    <input type="checkbox" id="contact_<?= $contact['id'] ?>" name="contacts[]" value="<?= $contact['id'] ?>"
                                        <?= $deal && in_array($contact['id'], $deal['contacts']) ? 'checked' : '' ?>>
                                    <label for="contact_<?= $contact['id'] ?>"><?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <?php $contact = $action === 'edit' ? $db->getContact($id) : null; ?>
                        <div class="form-group">
                            <label for="first_name">Имя*:</label>
                            <input type="text" id="first_name" name="first_name" value="<?= $contact ? htmlspecialchars($contact['first_name']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Фамилия:</label>
                            <input type="text" id="last_name" name="last_name" value="<?= $contact ? htmlspecialchars($contact['last_name']) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Сделки:</label>
                            <?php foreach ($deals as $deal): ?>
                                <div>
                                    <input type="checkbox" id="deal_<?= $deal['id'] ?>" name="deals[]" value="<?= $deal['id'] ?>"
                                        <?= $contact && in_array($deal['id'], $contact['deals']) ? 'checked' : '' ?>>
                                    <label for="deal_<?= $deal['id'] ?>"><?= htmlspecialchars($deal['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-actions">
                        <button type="submit" class="button">Сохранить</button>
                        <a href="index.php" class="button cancel">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
