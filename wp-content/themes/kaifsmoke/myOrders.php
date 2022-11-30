<?php
/*
    Template Name: Мои заказы
*/
?>
<?php
get_header();
?>
<?
$orders = VZUser::getOrders();
if (empty($orders['out']['orders'])) {
?>
    <section class="answerPage notHaveBlock">
        <div class="container">
            <div class="answerPage_block">
                <h1>Список заказов пуст</h1>
                <img src="/wp-content/themes/kaifsmoke/assets/images/icons/NotFoundHave.png" alt="">
                <a class="onHomePage" href="/catalog">Перейти в каталог</a>
            </div>
        </div>
    </section>
<?
} else {
    $orders = $orders['out']['orders'];
?>
    <section class="ordersPage">
        <div class="container">
            <div class="section_title">
                <h2>Заказы</h2>
            </div>
            <div class="breadcrumbs">
                <ul>
                    <?php true_breadcrumbs(); ?>
                </ul>
            </div>
            <div class="ordersPage_block">
                <?php
                foreach ($orders as $order) {
                    //echo'<pre>';var_export($order);echo'</pre>';
                    $status = [
                        'class' => '',
                        'name' => ''
                    ];
                    switch ($order['status']) {
                        case 'processing':
                            $status = [
                                'class' => 'open',
                                'name' => 'Открыт',
                                'icon' => '
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#2074C1" />
                                    <path
                                        d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                        fill="white" />
                                </svg>
                                '
                            ];
                            break;
                        case 'pending':
                            $status = [
                                'class' => 'expects',
                                'name' => 'Ожидает покупателя',
                                'icon' => '
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#31A337" />
                                    <path
                                        d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                        fill="white" />
                                </svg>
                                '
                            ];
                            break;
                        case 'cancelled':
                            $status = [
                                'class' => 'canceled',
                                'name' => 'Отменено',
                                'icon' => '
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#E9420D" />
                                    <path d="M7 7L13 13M13 7L10 10L7 13" stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                '
                            ];
                            break;
                        case 'completed':
                            $status = [
                                'class' => 'received',
                                'name' => 'Получено',
                                'icon' => '
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#1D1D1B" />
                                    <path
                                        d="M6.7348 9.65505C6.3602 9.24923 5.72754 9.22393 5.32172 9.59853C4.9159 9.97313 4.89059 10.6058 5.2652 11.0116L6.7348 9.65505ZM8.46154 13L7.72674 13.6783C7.91604 13.8834 8.18244 14 8.46154 14C8.74064 14 9.00704 13.8834 9.19634 13.6783L8.46154 13ZM14.7348 7.67828C15.1094 7.27246 15.0841 6.6398 14.6783 6.2652C14.2725 5.89059 13.6398 5.9159 13.2652 6.32172L14.7348 7.67828ZM5.2652 11.0116L7.72674 13.6783L9.19634 12.3217L6.7348 9.65505L5.2652 11.0116ZM9.19634 13.6783L14.7348 7.67828L13.2652 6.32172L7.72674 12.3217L9.19634 13.6783Z"
                                        fill="white" />
                                </svg>
                                '
                            ];
                            break;
                        case 'refunded':
                            $status = [
                                'class' => '',
                                'name' => 'Возвращён'
                            ];
                            break;
                        case 'failed':
                            $status = [
                                'class' => '',
                                'name' => 'Не удался'
                            ];
                            break;
                        case 'on-hold':
                            $status = [
                                'class' => 'delivery',
                                'name' => 'Доставка в магазин',
                                'icon' => '
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="10" fill="#2074C1" />
                                    <path
                                        d="M14 10L14.7071 10.7071C15.0976 10.3166 15.0976 9.68342 14.7071 9.29289L14 10ZM10.2929 12.2929C9.90237 12.6834 9.90237 13.3166 10.2929 13.7071C10.6834 14.0976 11.3166 14.0976 11.7071 13.7071L10.2929 12.2929ZM11.7071 6.29289C11.3166 5.90237 10.6834 5.90237 10.2929 6.29289C9.90237 6.68342 9.90237 7.31658 10.2929 7.70711L11.7071 6.29289ZM13.2929 9.29289L10.2929 12.2929L11.7071 13.7071L14.7071 10.7071L13.2929 9.29289ZM14.7071 9.29289L11.7071 6.29289L10.2929 7.70711L13.2929 10.7071L14.7071 9.29289Z"
                                        fill="white" />
                                    <path
                                        d="M10 10L10.7071 10.7071C11.0976 10.3166 11.0976 9.68342 10.7071 9.29289L10 10ZM6.29289 12.2929C5.90237 12.6834 5.90237 13.3166 6.29289 13.7071C6.68342 14.0976 7.31658 14.0976 7.70711 13.7071L6.29289 12.2929ZM7.70711 6.29289C7.31658 5.90237 6.68342 5.90237 6.29289 6.29289C5.90237 6.68342 5.90237 7.31658 6.29289 7.70711L7.70711 6.29289ZM9.29289 9.29289L6.29289 12.2929L7.70711 13.7071L10.7071 10.7071L9.29289 9.29289ZM10.7071 9.29289L7.70711 6.29289L6.29289 7.70711L9.29289 10.7071L10.7071 9.29289Z"
                                        fill="white" />
                                </svg>
                                '
                            ];
                            break;
                    }
                ?>
                    <div class="ordersPage_block__order">
                        <div class="order_status">
                            <div class="icon">
                                <?= $status['icon'] ?>
                            </div>
                            <span class="<?= $status['class'] ?>">
                                <?= $status['name'] ?>
                            </span>
                        </div>
                        <div class="order_properties">
                            <div class="order_properties__property">
                                <span class="property_name">
                                    Номер заказа:
                                </span>
                                <span class="property_value">
                                    #<?= $order['id'] ?>
                                </span>
                            </div>
                            <div class="order_properties__property">
                                <span class="property_name">
                                    Дата заказа:
                                </span>
                                <span class="property_value">
                                    <?= $order['date']->date('d.m.Y') ?>
                                </span>
                            </div>
                            <div class="order_properties__property">
                                <span class="property_name">
                                    Сумма:
                                </span>
                                <span class="property_value">
                                    <?= $order['price'] ?> руб
                                </span>
                            </div>
                        </div>
                        <div class="order_images">
                            <?php foreach ($order['products'] as $product) { ?>
                                <div class="order_images__img">
                                    <img src="<?= $product['image_link'] ?>" alt="">
                                </div>
                            <?php } ?>
                        </div>
                        <div class="order_moreDetails">
                            <a href="<?= $order['link'] ?>" class="moreDetails_link btn primary s">Подробнее</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?
}
?>
<?php
get_footer();
?>