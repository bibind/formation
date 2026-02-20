<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\Category;
use App\Domain\Order;
use App\Domain\OrderLine;
use App\Domain\Product;
use App\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class MarketplaceApiTest extends ApiTestCase
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->purgeDatabase();
    }

    protected static function createKernel(array $options = []): \Symfony\Component\HttpKernel\KernelInterface
    {
        require_once dirname(__DIR__, 2) . '/src/Kernel.php';
        $env = $options['environment'] ?? 'test';
        $debug = $options['debug'] ?? true;
        return new \App\Kernel($env, $debug);
    }

    public function test_auth_jwt_obtain_token(): void
    {
        $user = $this->createUser('seller@example.test', 'Password123!', ['ROLE_USER']);

        $client = static::createClient();
        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => $user->email(),
                'password' => 'Password123!',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertArrayHasKey('token', $data);
    }

    public function test_seller_can_create_product(): void
    {
        $user = $this->createUser('seller2@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_1', 'T-shirts');
        $token = $this->getToken($user->email(), 'Password123!');

        $client = static::createClient();
        $client->request('POST', '/api/products', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'name' => 'Blue T-shirt',
                'categoryId' => $category->id(),
                'stock' => 10,
                'price' => 2500,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function test_seller_cannot_edit_other_seller_product(): void
    {
        $sellerA = $this->createUser('sellerA@example.test', 'Password123!', ['ROLE_USER']);
        $sellerB = $this->createUser('sellerB@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_2', 'Shoes');
        $product = $this->createProduct('prod_1', 'Sneakers', $category, $sellerA, 5, 8000);

        $token = $this->getToken($sellerB->email(), 'Password123!');
        $client = static::createClient();
        $client->request('PUT', '/api/products/' . $product->id(), [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'name' => 'Hacked',
                'categoryId' => $category->id(),
                'stock' => 5,
                'price' => 8000,
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function test_order_create_ok_decrements_stock_when_validated(): void
    {
        $buyer = $this->createUser('buyer@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_3', 'Mugs');
        $product = $this->createProduct('prod_2', 'Mug', $category, $buyer, 10, 1500);
        $token = $this->getToken($buyer->email(), 'Password123!');

        $client = static::createClient();
        $client->request('POST', '/api/orders', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'lines' => [
                    ['productId' => $product->id(), 'quantity' => 2],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $updatedProduct = $this->em->getRepository(Product::class)->find($product->id());
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertSame(8, $updatedProduct->stock());
    }

    public function test_order_create_fails_when_insufficient_stock(): void
    {
        $buyer = $this->createUser('buyer2@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_4', 'Caps');
        $product = $this->createProduct('prod_3', 'Cap', $category, $buyer, 1, 1200);
        $token = $this->getToken($buyer->email(), 'Password123!');

        $client = static::createClient();
        $client->request('POST', '/api/orders', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'lines' => [
                    ['productId' => $product->id(), 'quantity' => 2],
                ],
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function test_upload_image_ok(): void
    {
        $seller = $this->createUser('seller3@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_5', 'Posters');
        $product = $this->createProduct('prod_4', 'Poster', $category, $seller, 3, 5000);
        $token = $this->getToken($seller->email(), 'Password123!');

        $filePath = sys_get_temp_dir() . '/img_ok.png';
        file_put_contents($filePath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg=='));
        $upload = new UploadedFile($filePath, 'img_ok.png', 'image/png', null, true);

        $client = static::createClient();
        $client->request('POST', '/api/products/' . $product->id() . '/image', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'extra' => ['files' => ['file' => $upload]],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function test_upload_image_fails_wrong_mime_or_too_large(): void
    {
        $seller = $this->createUser('seller4@example.test', 'Password123!', ['ROLE_USER']);
        $category = $this->createCategory('cat_6', 'Stickers');
        $product = $this->createProduct('prod_5', 'Sticker', $category, $seller, 5, 300);
        $token = $this->getToken($seller->email(), 'Password123!');

        $filePath = sys_get_temp_dir() . '/bad.txt';
        file_put_contents($filePath, 'not an image');
        $upload = new UploadedFile($filePath, 'bad.txt', 'text/plain', null, true);

        $client = static::createClient();
        $client->request('POST', '/api/products/' . $product->id() . '/image', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'extra' => ['files' => ['file' => $upload]],
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    private function getToken(string $email, string $password): string
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]);

        $data = $response->toArray(false);
        return $data['token'] ?? '';
    }

    private function createUser(string $email, string $plainPassword, array $roles): User
    {
        $user = new User(bin2hex(random_bytes(16)), $email, $roles, '');
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    private function createCategory(string $id, string $name): Category
    {
        $category = new Category($id, $name);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    private function createProduct(string $id, string $name, Category $category, User $owner, int $stock, int $price): Product
    {
        $product = new Product($id, $name, $category, $owner, $stock, $price);
        $this->em->persist($product);
        $this->em->flush();
        return $product;
    }

    private function purgeDatabase(): void
    {
        $this->em->createQuery('DELETE FROM ' . OrderLine::class . ' l')->execute();
        $this->em->createQuery('DELETE FROM ' . Order::class . ' o')->execute();
        $this->em->createQuery('DELETE FROM ' . Product::class . ' p')->execute();
        $this->em->createQuery('DELETE FROM ' . Category::class . ' c')->execute();
        $this->em->createQuery('DELETE FROM ' . User::class . ' u')->execute();
    }
}
