<!DOCTYPE html>
<html lang="en">
<head>
  <title>Item</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<div class="container mt-3">
  <h2>Data Item</h2>
  <button type="button" class="btn btn-success"
  data-bs-toggle="modal" data-bs-target="#modaltambah" id="tambah" onclick="tambah()">ADD</button>
  <table class="table table-striped" id="tableitem">
    <thead>
      <tr>
      <th>Action</th> <th>Id</th> <th>Name</th> <th>Description</th> <th>Price</th><th>Stock</th>
      </tr>
    </thead>
    <tbody>
      @foreach($item as $x)
      <tr>
        <td>
          <!-- Di tabel hanya ada tombol Edit yang memanggil function -->
          <button type="button" class="btn btn-success" onclick="edit('{{ $x->id }}', '{{ addslashes($x->name) }}', '{{ addslashes($x->description ?? '') }}', '{{ $x->price }}', '{{ $x->stock }}')">Edit</button>
        </td>
        <td>{{{ $x->id }}}</td>
        <td>{{{ $x->name }}}</td>
        <td>{{{ $x->description }}}</td>
        <td>{{{ $x->price }}}</td>
        <td>{{{ $x->stock }}}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- The Modal -->
<div class="modal" id="modaltambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title" id="modalTitle">Tambah Item</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <input type="hidden" id="id" name="id">
        <div class="mt-3">
          <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
        </div>
        <div class="mt-3">
          <input type="text" class="form-control" id="description" placeholder="Enter Description" name="description">
        </div>
        <div class="mt-3">
          <input type="number" class="form-control" id="price" placeholder="Enter Price" name="price">
        </div>
        <div class="mt-3">
          <input type="number" class="form-control" id="stock" placeholder="Enter Stock" name="stock">
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save1">Save</button>
        <!-- Tombol Delete di dalam Modal Edit -->
        <button type="button" class="btn btn-danger" id="btnDelete" style="display: none;">Delete</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  const csrfToken = $('meta[name="csrf-token"]').attr('content');

  // 1 FUNGSI AJAX UTAMA (Dipakai untuk Tambah, Update, & Delete)
  function kirimAjax(url, formData) {
    $.ajax({
      type: 'POST',
      url: url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status) {
          alert(response.message);
          window.location.reload();
        } else {
          alert(response.errors ? Object.values(response.errors).join('\n') : 'Gagal memproses data!');
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON;
        alert(response?.message || (response?.errors ? Object.values(response.errors).join('\n') : 'Terjadi kesalahan!'));
      }
    });
  }

  // Fungsi saat tombol ADD diklik: reset form dan sembunyikan tombol delete
  function tambah() {
    $('#id').val('');
    $('#name').val('');
    $('#description').val('');
    $('#price').val('');
    $('#stock').val('');
    $('#modalTitle').text('Tambah Item');
    $('#save1').text('Save');
    $('#btnDelete').hide();
  }

  // FUNGSI EDIT: Menambahkan value (.val) sesuai id ke modal & memunculkan tombol delete
  function edit(id, name, description, price, stock) {
    $('#id').val(id);
    $('#name').val(name);
    $('#description').val(description);
    $('#price').val(price);
    $('#stock').val(stock);
    $('#modalTitle').text('Edit Item');
    $('#save1').text('Update');
    $('#btnDelete').show();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modaltambah')).show();
  }

  // FUNGSI SIMPAN: Menangani Tambah (POST) & Update (PUT via spoofing)
  $('#save1').on('click', function () {
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('id', $('#id').val());
    formData.append('name', $('#name').val());
    formData.append('description', $('#description').val());
    formData.append('price', $('#price').val());
    formData.append('stock', $('#stock').val());

    // Jika sedang edit/update, tambahkan _method PUT
    if ($('#id').val()) {
      formData.append('_method', 'PUT');
    }

    // URL tetap bersih {{ url('/items') }} tanpa + id
    kirimAjax('{{ url("/items") }}', formData);
  });

  // FUNGSI DELETE DARI DALAM MODAL: Mengambil id dari modal dan spoofing DELETE
  $('#btnDelete').on('click', function () {
    const id = $('#id').val();
    if (!id || !confirm('Apakah anda yakin ingin menghapus item ini?')) {
      return;
    }

    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('id', id);
    formData.append('_method', 'DELETE'); // Spoofing DELETE

    // URL tetap bersih {{ url('/items') }} tanpa + id
    kirimAjax('{{ url("/items") }}', formData);
  });
</script>
</body>
</html>
