@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="main">
    <section class="page__text-block text-block">
        <div class="text-block__container">
            <h2 class="text-block__title title" id="scroll">Bonus & Referral Program</h2>
            <div class="text-block__body">
                <h3 class = "ship_">Bonus Program</h3>
                <br>
                <p>Bonus card gives you great opportunities to receive discounts and take part in major sales.</p>
                <p style="line-height: 1.5">
                    You will receive a bonus card after your first order. You can use your bonuses to pay for your future purchases. To pay for an order with bonuses, enter your bonus card number on the payment page.
                </p>
                <ul style="line-height: 1.5">
                    Bonus cards have three status levels:
                    <li>1) Silver</li>
                    <li>2) Gold</li>
                    <li>3) VIP</li>
                </ul>
                <br>
                <p style="line-height: 1.8">
                    With each order paid for using a <b>Silver</b> card, you will receive 5% of the order amount in bonuses.<br>
                    With a <b>Gold</b> card — 7%.<br>
                    With a <b>VIP</b> card — 10%.
                </p>
                <p style="line-height: 1.8">
                    The card status is upgraded depending on the number of orders placed.<br>
                    A <b>Gold</b> card is granted after 5 orders per year.<br>
                    A <b>VIP</b> card is granted after 7 orders per year.
                </p>
                <p><b>Bonuses are not credited if you use discount coupons.</b></p>

                <h3 class = "ship_">Referral Program</h3>
                <br>
                <p style="line-height: 1.5">
                    You can earn money by recommending our store to your friends and acquaintances. To do this, after placing an order, go to your personal account, which is created after your first order. There, you will find the <b>Referral Program</b> section, where you can get your personal link with your ID.
                </p>
                <p style="line-height: 1.5">
                    For every sale made through your link, you will receive 5% of the order amount. These earnings will be displayed in your account balance, and you will be able to request a withdrawal of this amount to your payment details.
                </p>
            </div>
        </div>
    </section>
</div>
</div>
</div>
</div>

@endsection