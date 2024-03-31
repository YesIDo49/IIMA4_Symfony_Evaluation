<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Large cotton tote bag pasta');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('This large cotton tote bag is perfect for carrying your groceries in style. Its durable material and spacious design make it ideal for everyday use.');
        $product->setPhoto('product1.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Uniball One F Kirby');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Get creative with the Uniball One F Kirby pen. Its smooth ink flow and comfortable grip make it a favorite among artists and writers alike.');
        $product->setPhoto('product2.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Rollbahn Pocket Memo L Kirby Waddle Dee');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Stay organized on the go with the Rollbahn Pocket Memo L Kirby Waddle Dee. With its compact size and stylish design, it\'s perfect for jotting down notes wherever you are.');
        $product->setPhoto('product3.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Waffle drawstring set');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Indulge in cozy comfort with our waffle drawstring set. Made from soft, breathable fabric, it\'s the perfect loungewear for lazy weekends at home.');
        $product->setPhoto('product4.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Original pasta shiro');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Experience the authentic taste of Italy with our original pasta shiro. Made from the finest ingredients and traditional recipes, it\'s a pasta lover\'s dream come true.');
        $product->setPhoto('product5.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Ohoshisamasabei Pepper');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Add a burst of flavor to your dishes with Ohoshisamasabei Pepper. Made from a blend of premium spices, it\'s the perfect seasoning for any meal.');
        $product->setPhoto('product6.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Fluffy mascot Waddle Dee');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Bring home your very own Fluffy mascot Waddle Dee. This adorable plush toy is perfect for Kirby fans of all ages.');
        $product->setPhoto('product7.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Kirky car cheese cake key chain');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Add a touch of sweetness to your keys! This Kirky car cheese cake key chain is the perfect accessory for Kirby fans.');
        $product->setPhoto('product8.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Kirky coffee mug');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Start your day with a cup of coffee in this adorable Kirky coffee mug. Its cute design and sturdy construction make it a must-have.');
        $product->setPhoto('product9.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Kirky chef plushie');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Get cooking with the Kirky chef plushie. This cute and cuddly toy is perfect for Kirby fans who love to spend time in the kitchen.');
        $product->setPhoto('product10.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Kirky tea can');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Store your favorite tea in this adorable Kirky tea can. Its airtight lid and cute design make it the perfect addition to any kitchen.');
        $product->setPhoto('product11.jpg');
        $manager->persist($product);

        $product = new Product();
        $product->setName('Kirky Stainless steel tumbler');
        $product->setPrice(mt_rand(1000, 10000) / 100);
        $product->setStock(rand(0, 10));
        $product->setDescription('Stay hydrated on the go with the Kirky stainless steel tumbler. Its durable construction and cute design make it the perfect travel companion.');
        $product->setPhoto('product12.jpg');
        $manager->persist($product);

        $manager->flush();
    }
}
