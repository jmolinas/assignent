<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      text-align: left;
    }

    /* Initial state for rows */
    .hidden-row {
      opacity: 0;
      transform: translateX(-100%);
      /* Start off-screen */
      transition: all 0.5s ease;
      /* Smooth transition */
    }

    /* Final state for visible rows */
    .visible-row {
      opacity: 1;
      transform: translateX(0);
      transition: all 0.5s ease;
      /* Move to its normal position */
    }
  </style>

</head>

<body class="py-4">
  <main>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-header">
            <h1>Search Books</h1>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
          <form id="api-search" class="form-inline input-group mb-3" role="form">
            <input type="text" id="query" name="query" class="form-control" placeholder="Search for books by author" aria-label="Search for books by author" aria-describedby="search">
            <button class="btn btn-outline-secondary" type="submit" id="search">search</button>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <table id="data-table">
            <tbody>
              <!-- Data will be dynamically added -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script src="/js/script.js"></script>
</body>

</html>