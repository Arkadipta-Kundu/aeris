<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAuth();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $sku = sanitizeInput($_POST['sku']);
                $title = sanitizeInput($_POST['title']);
                $description = sanitizeInput($_POST['description']);
                $supplier_name = sanitizeInput($_POST['supplier_name']);
                $cost_price = floatval($_POST['cost_price']);
                $selling_price = floatval($_POST['selling_price']);
                $stock = intval($_POST['stock']);
                $image_url = sanitizeInput($_POST['image_url']);
                try {
                    $stmt = $pdo->prepare("INSERT INTO products (user_id, sku, title, description, supplier_name, cost_price, selling_price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $sku, $title, $description, $supplier_name, $cost_price, $selling_price, $stock, $image_url]);
                    setFlashMessage('success', 'Product added successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error adding product: ' . $e->getMessage());
                }
                break;

            case 'edit':
                $id = intval($_POST['id']);
                $sku = sanitizeInput($_POST['sku']);
                $title = sanitizeInput($_POST['title']);
                $description = sanitizeInput($_POST['description']);
                $supplier_name = sanitizeInput($_POST['supplier_name']);
                $cost_price = floatval($_POST['cost_price']);
                $selling_price = floatval($_POST['selling_price']);
                $stock = intval($_POST['stock']);
                $image_url = sanitizeInput($_POST['image_url']);
                try {
                    $stmt = $pdo->prepare("UPDATE products SET sku=?, title=?, description=?, supplier_name=?, cost_price=?, selling_price=?, stock=?, image_url=? WHERE id=? AND user_id=?");
                    $stmt->execute([$sku, $title, $description, $supplier_name, $cost_price, $selling_price, $stock, $image_url, $id, $_SESSION['user_id']]);
                    setFlashMessage('success', 'Product updated successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error updating product: ' . $e->getMessage());
                }
                break;

            case 'delete':
                $id = intval($_POST['id']);
                try {
                    $stmt = $pdo->prepare("DELETE FROM products WHERE id=? AND user_id=?");
                    $stmt->execute([$id, $_SESSION['user_id']]);
                    setFlashMessage('success', 'Product deleted successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error deleting product: ' . $e->getMessage());
                }
                break;
        }
        header('Location: products.php');
        exit();
    }
}

// Get all products for current user
$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Get product for editing if edit ID is provided
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$editId, $_SESSION['user_id']]);
    $editProduct = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Products</title>
    <link rel="icon" type="image/x-icon" href="asset/logo_new.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'includes/nav.php'; ?>

    <div class="min-h-screen pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Products</h1>
                    <p class="text-gray-600 mt-2">Manage your product catalog</p>
                </div>
                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                    Add Product
                </button>
            </div>

            <!-- Flash Messages -->
            <?php $flash = getFlashMessage(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $flash['type'] == 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'; ?>">
                    <div class="flex">
                        <i data-feather="<?php echo $flash['type'] == 'success' ? 'check-circle' : 'alert-circle'; ?>"
                            class="w-5 h-5 <?php echo $flash['type'] == 'success' ? 'text-green-500' : 'text-red-500'; ?> mt-0.5 mr-3"></i>
                        <p class="<?php echo $flash['type'] == 'success' ? 'text-green-700' : 'text-red-700'; ?> text-sm">
                            <?php echo htmlspecialchars($flash['message']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">All Products</h2>
                </div>

                <?php if (empty($products)): ?>
                    <div class="text-center py-12">
                        <i data-feather="package" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500 text-lg">No products yet</p>
                        <p class="text-gray-400 mt-2">Add your first product to get started</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pricing</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($products as $product): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php if ($product['image_url']): ?>
                                                    <img class="h-12 w-12 rounded-lg object-cover mr-4" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product">
                                                <?php else: ?>
                                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                                        <i data-feather="image" class="w-6 h-6 text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['title']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($product['sku']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>Cost: <?php echo formatPrice($product['cost_price']); ?></div>
                                            <div>Sell: <?php echo formatPrice($product['selling_price']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $product['stock'] > 10 ? 'bg-green-100 text-green-800' : ($product['stock'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                                <?php echo $product['stock']; ?> units
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                                    class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </button>
                                                <button onclick="deleteProduct(<?php echo $product['id']; ?>)"
                                                    class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add Product</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="productForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="productId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" id="productSku" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="productTitle" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="productDescription" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                    <input type="text" name="supplier_name" id="productSupplier" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                        <input type="number" name="cost_price" id="productCostPrice" step="0.01" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                        <input type="number" name="selling_price" id="productSellingPrice" step="0.01" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock" id="productStock" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URL (optional)</label>
                    <input type="url" name="image_url" id="productImageUrl"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-150 ease-in-out">
                        <i data-feather="save" class="w-4 h-4 mr-2 inline"></i>
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <i data-feather="alert-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Product</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this product? This action cannot be undone.</p>

                <form id="deleteForm" method="POST" class="flex justify-center space-x-3">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteProductId">

                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition duration-150 ease-in-out">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';

            // Generate SKU
            document.getElementById('productSku').value = 'SKU' + Math.random().toString(36).substr(2, 6).toUpperCase();

            document.getElementById('productModal').classList.remove('hidden');
        }

        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = product.id;
            document.getElementById('productSku').value = product.sku;
            document.getElementById('productTitle').value = product.title;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productSupplier').value = product.supplier_name;
            document.getElementById('productCostPrice').value = product.cost_price;
            document.getElementById('productSellingPrice').value = product.selling_price;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productImageUrl').value = product.image_url || '';

            document.getElementById('productModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        function deleteProduct(productId) {
            document.getElementById('deleteProductId').value = productId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>