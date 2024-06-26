@extends('layouts.admin')

@section('title')
    Cập nhật hóa đơn nhập
@endsection

@section('style-plugins')
    <link rel="stylesheet" href="{{ asset('src/plugins/src/sweetalerts2/sweetalerts2.css') }}">

    <link href="{{ asset('src/assets/css/light/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('src/plugins/css/light/sweetalerts2/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('src/assets/css/dark/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('src/plugins/css/dark/sweetalerts2/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/src/tomSelect/tom-select.default.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/css/light/tomSelect/custom-tomSelect.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/css/dark/tomSelect/custom-tomSelect.css') }}">
@endsection

@section('script-plugins')
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="{{ asset('src/plugins/src/sweetalerts2/sweetalerts2.min.js') }}"></script>

    @include('includes.toast')

    <script src="{{ asset('src/plugins/src/tomSelect/tom-select.base.js') }}"></script>
    <script src="{{ asset('src/plugins/src/tomSelect/custom-tom-select.js') }}"></script>
@endsection

@section('content')
    <div class="layout-top-spacing col-12">
        <a href="{{ route('admin.purchase_order.index') }}" class="btn btn-default _effect--ripple waves-effect waves-light">
            Trở lại
        </a>
    </div>
    <div class="row layout-top-spacing ">
        <div id="supplier-management" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Cập nhật hóa đơn nhập</h4>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area" style="padding: 20px !important;">
                    <div class="col-lg-12">
                        <form id="general-settings" method="POST" action="{{ route('admin.purchase_order.update', $purchaseOrder) }}">
                            @csrf
                            @method("PUT")
                            <div class="form-group mb-4">
                                <label for="supplier_id">Nhà cung cấp <strong class="text-danger">*</strong>
                                </label></label>
                                <select class="form-select" id="supplier_id" name="supplier_id">
                                    <option value="">Lựa chọn</option>
                                    @foreach ($suppliers as $supplier)
                                        <option
                                            @selected($supplier->id == (old('supplier_id') || $purchaseOrder->supplier_id))
                                            value="{{ $supplier->id }}"
                                        >
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="note">Ghi chú <strong class="text-danger">*</strong>
                                </label>
                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" id="note"
                                    rows="3" placeholder="Ghi chú" spellcheck="false" @error('note') is-invalid @enderror>{{ old('note') ?? $purchaseOrder->note }}</textarea>
                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="note">Sản phẩm được nhập <strong class="text-danger">*</strong>
                                </label>
                                <select class="form-control" id="select-products" multiple placeholder="Chọn một sản phẩm..." autocomplete="off">
                                    <option value="">Chọn một sản phẩm...</option>
                                    @foreach ($products as $product)
                                        <option
                                            @selected(false) value="{{ $product->id }}"
                                        >
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="product-group">
                            </div>
                            <button type="submit" class="btn btn-primary _effect--ripple waves-effect waves-light">
                                Hoàn tất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="purchase_order_products" value="{{ json_encode($purchaseOrderProducts) }}">
    <input type="hidden" id="product_id" value="{{ json_encode(old('product_id')) }}">
    <input type="hidden" id="product_quantity" value="{{ json_encode(old('product_quantity')) }}">
@endsection

@section('script')
    <script>
        let tomSelectProducts = new TomSelect("#select-products");

        let productItem = `
            <div class="row mb-4 product-item">
                <input type="hidden" name="product_id[]" class="product_id">
                <div class="col-md-9">
                    <input
                        type="text"
                        name="product_name[]"
                        class="form-control text-dark product_name"
                        placeholder="Tên sản phẩm"
                        readonly
                    >
                </div>
                <div class="col-md-3">
                    <input
                        type="number"
                        min="1"
                        required
                        name="product_quantity[]"
                        class="form-control
                        product_quantity"
                        placeholder="Số lượng"
                    >
                </div>
            </div>
        `;

        function debounce(func, timeout = 300){
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => { func.apply(this, args); }, timeout);
                };
            }

        function handleChangeTomSelect(){
            let selectedValues = tomSelectProducts.getValue();
            let options = tomSelectProducts.options;
            let productGroup = document.querySelector(".product-group");
            let productItems = productGroup.querySelectorAll(".product-item");

            if(productItems.length){
                productItems.forEach(productItem => {
                    let productId = productItem.getAttribute("data-product-id");
                    if(!selectedValues.includes(productId)){
                        productItem.remove();
                    }
                    else {
                        selectedValues = selectedValues.filter(value => value != productId);
                    }
                });
            }

            if(selectedValues.length){
                selectedValues.forEach((value, index) => {
                    let productItemNode = new DOMParser()
                    .parseFromString(productItem, "text/html")
                    .querySelector(".product-item");
                    let productName = options[value].text.trim();

                    productItemNode.setAttribute("data-product-id", value);
                    productItemNode.querySelector(".product_name").value = productName;
                    productItemNode.querySelector(".product_id").value = value;

                    if(JSON.parse($('#product_quantity').val())){
                        let values = JSON.parse($('#product_quantity').val());
                        productItemNode.querySelector(".product_quantity").value = values[index];
                    }
                    else
                    {
                        if(JSON.parse($('#purchase_order_products').val())){
                            let values = JSON.parse($('#purchase_order_products').val());
                            values.forEach(item => {
                                if(item.id == value){
                                    productItemNode.querySelector(".product_quantity").value = item.pivot.quantity;
                                }
                            });
                        }
                    }

                    productGroup.appendChild(productItemNode);
                });
            }
        }

        const processChange = debounce(() => handleChangeTomSelect());

        tomSelectProducts.on("change", function() {
            processChange();
        });

        if(JSON.parse($('#product_id').val())){
            let values = JSON.parse($('#product_id').val());
            values.forEach(value => tomSelectProducts.addItem(value));
        }
        else
        {
            if(JSON.parse($('#purchase_order_products').val())){
                let values = JSON.parse($('#purchase_order_products').val());
                values.forEach(value => tomSelectProducts.addItem(value.id));
            }
        }
    </script>
@endsection
