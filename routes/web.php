<?php


use App\Models\Produto;
use App\Models\Favorito;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminProdutoController;
use App\Http\Controllers\AdminPedidosController;
use App\Http\Controllers\AdminFaqController;
use App\Http\Controllers\AdminPedidoDuvidaController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PedidoDuvidaController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    $destaques = Produto::where('destaque', 1)->get();
    $produtosGerais = Produto::all();
    $categorias = Produto::distinct()->pluck('categoria');
    $favoritosIds = auth()->check()
        ? Favorito::query()
            ->where('user_id', auth()->id())
            ->pluck('produto_id')
            ->map(static fn ($produtoId): int => (int) $produtoId)
            ->all()
        : [];

    return view('index', compact('destaques', 'produtosGerais', 'categorias', 'favoritosIds'));
});

Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Página pública de produto
use App\Http\Controllers\PublicProdutoController;
use App\Http\Controllers\SearchController;
Route::get('/produtos/{produto}', [PublicProdutoController::class, 'show'])->name('produtos.show');
Route::get('/search', SearchController::class)->name('search');
Route::get('/api/carrinho', [CarrinhoController::class, 'getCarrinho'])->name('carrinho.get');
Route::post('/carrinho/{produto}', [CarrinhoController::class, 'store'])->name('carrinho.add');
Route::put('/carrinho/{produto}', [CarrinhoController::class, 'update'])->name('carrinho.update');
Route::delete('/carrinho/{produto}', [CarrinhoController::class, 'destroy'])->name('carrinho.remove');
Route::delete('/carrinho', [CarrinhoController::class, 'clear'])->name('carrinho.clear');
// Checkout exige login: todo pedido fica vinculado a uma conta, o que permite
// acompanha-lo em "Meus Pedidos" e enviar duvidas sobre ele.
Route::middleware('auth')->group(function (): void {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/processar', fn () => redirect()->route('checkout.index'));
    Route::post('/checkout/processar', [CheckoutController::class, 'finalizar'])->name('checkout.processar');
    Route::get('/checkout/pix/{referencia}', [CheckoutController::class, 'pix'])
        ->where('referencia', '[A-Za-z0-9\-]+')
        ->name('checkout.pix');
    Route::get('/checkout/pix/{referencia}/status', [CheckoutController::class, 'pixStatus'])
        ->where('referencia', '[A-Za-z0-9\-]+')
        ->name('checkout.pix.status');
});
// Retorno do Mercado Pago fica publico: o status exibido e sempre consultado na API do MP.
Route::get('/checkout/retorno', [CheckoutController::class, 'retorno'])->name('checkout.retorno');

// Webhook do Mercado Pago: CSRF isento em bootstrap/app.php. O status e sempre
// confirmado consultando a API do MP (ver MercadoPagoWebhookController).
Route::match(['get', 'post'], '/webhooks/mercadopago', MercadoPagoWebhookController::class)
    ->name('webhooks.mercadopago');

Route::middleware('auth')->group(function (): void {
    Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/{produto}', [FavoritoController::class, 'store'])->name('favoritos.store');
    Route::delete('/favoritos/{produto}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos/{pedido}/duvidas', [PedidoDuvidaController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('pedidos.duvidas.store');
});

//Rotas de Conta
Route::middleware('auth')->group(function (): void {
    Route::get('/meusdados', [UserController::class, 'meusdados'])->name('meusdados');
    Route::post('/meusdados/update', [UserController::class, 'atualizarMeusDados'])->name('meusdados.update');
});

// Rotas de Trocar Senha
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [UserController::class, 'changePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');
});

// Usuário DESLOGADO: esqueci minha senha
Route::middleware('guest')->group(function () {
    Route::post('/esqueci-senha', [ForgotPasswordController::class, 'sendLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/redefinir-senha/{token}', [ResetPasswordController::class, 'showForm'])
        ->name('password.reset');

    Route::post('/redefinir-senha', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});



// Rotas de Registro
Route::get('/empresa/{cnpj}', [EmpresaController::class, 'consultarCnpj']);
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Rotas de Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas de Admin (Painel de Administração)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminProdutoController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [AdminProdutoController::class, 'dashboardData'])->name('dashboard.data');
    Route::get('/dashboard/export', [AdminProdutoController::class, 'exportCsv'])->name('dashboard.export');
    Route::get('/dashboard/print', [AdminProdutoController::class, 'printReport'])->name('dashboard.print');
    Route::get('/relatorios', [AdminProdutoController::class, 'relatorios'])->name('relatorios');
    Route::get('/produtos/search', [AdminProdutoController::class, 'search'])->name('produtos.search');

    // Pedidos
    Route::get('/pedidos', [AdminPedidosController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{id}', [AdminPedidosController::class, 'show'])->name('pedidos.show');

    // Atendimento: duvidas dos clientes sobre pedidos
    Route::get('/duvidas', [AdminPedidoDuvidaController::class, 'index'])->name('duvidas.index');
    Route::get('/duvidas/{duvida}', [AdminPedidoDuvidaController::class, 'show'])->name('duvidas.show');
    Route::put('/duvidas/{duvida}', [AdminPedidoDuvidaController::class, 'update'])->name('duvidas.update');

    // FAQ
    Route::resource('faqs', AdminFaqController::class)->except('show');
    Route::patch('/faqs/{faq}/toggle', [AdminFaqController::class, 'toggle'])->name('faqs.toggle');

    // CRUD de Produtos
    Route::resource('produtos', AdminProdutoController::class);

    // Ações especiais
    Route::patch('/produtos/{produto}/toggle', [AdminProdutoController::class, 'toggle'])->name('produtos.toggle');
    Route::patch('/produtos/{produto}/toggle-destaque', [AdminProdutoController::class, 'toggleDestaque'])->name('produtos.toggleDestaque');
});
