<?php

require_once '../includes/_header.php'; ?>

  <main class="py-5">
    <div class="container">
      <form>
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name">
        </div>
        <div class="mb-3">
          <label for="release_date" class="form-label">Release Date</label>
          <input type="date" class="form-control" id="release_date">
        </div>
        <div class="mb-3">
          <label for="actors" class="form-label">Actors</label>
          <select class="form-select" name="actors" id="actors" multiple aria-label="Multiple select example">
            <option selected>Select Actors</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="format" class="form-label">Format</label>
          <select class="form-select" name="format" id="format" aria-label="Multiple select example">
            <option selected>Select Format</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </main>

<?php

require_once '../includes/_footer.php'; ?>