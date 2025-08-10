<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>AI Prediction Tool</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  /* Background & hero style */
  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  /* Overlay for darkening */
  body::before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(31, 41, 55, 0.7);
    z-index: 0;
  }

  /* Transparent card container */
  .card {
    background: rgba(255 255 255 / 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    padding: 3rem 2.5rem;
    width: 100%;
    max-width: 700px;
    color: #f9f9f9;
    position: relative;
    z-index: 1;
  }

  /* Headings */
  h3 {
    font-weight: 800;
    font-size: 3rem;
    text-align: center;
    margin-bottom: 2rem;
    text-shadow: 0 0 15px rgba(0,0,0,0.6);
  }

  /* Labels & inputs */
  label {
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 0.6rem;
    display: inline-block;
    text-shadow: 0 0 3px rgba(0,0,0,0.6);
  }

  textarea, input[type="text"], input[type="number"] {
    border-radius: 12px !important;
    border: none !important;
    background: rgba(255 255 255 / 0.3);
    color: #fff;
    padding: 1.1rem 1.25rem;
    font-size: 1.15rem;
    box-shadow: inset 0 0 8px rgba(255,255,255,0.3);
    transition: background-color 0.3s ease, color 0.3s ease;
    width: 100%;
    resize: vertical;
    min-height: 70px;
  }
  textarea::placeholder,
  input::placeholder {
    color: #ddd;
    font-style: italic;
  }
  textarea:focus, input:focus {
    background: rgba(255 255 255 / 0.55);
    outline: none;
    color: #222;
    box-shadow: 0 0 8px #4a69bd;
  }

  /* Spacing between inputs */
  .form-group {
    margin-bottom: 1.8rem;
  }

  /* Button */
  .btn-custom {
    background-color: #4a69bd;
    color: white;
    border-radius: 40px;
    font-weight: 800;
    font-size: 1.3rem;
    padding: 1rem 3rem;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 0 25px #4a69bdbb;
    letter-spacing: 1px;
    user-select: none;
    display: inline-block;
    min-width: 220px;
  }
  .btn-custom:hover, .btn-custom:focus {
    background-color: #3753a3;
    transform: scale(1.07);
    box-shadow: 0 0 35px #3753a3dd;
  }
  .btn-custom:active {
    transform: scale(0.98);
  }

  /* Modal styles override */
  .modal-content {
    background: rgba(255 255 255 / 0.95);
    color: #222;
    border-radius: 15px;
  }

  .modal-header, .modal-footer {
    border: none;
  }

  /* Responsive typography */
  @media (max-width: 768px) {
    h3 {
      font-size: 2.2rem;
    }
    .btn-custom {
      min-width: 100%;
      padding: 1rem 0;
      font-size: 1.1rem;
    }
  }
  @media (max-width: 480px) {
    .card {
      padding: 2rem 1.5rem;
    }
    textarea, input[type="text"], input[type="number"] {
      font-size: 1rem;
      padding: 0.9rem 1rem;
      min-height: 60px;
    }
  }
</style>
</head>
<body>

<div class="card shadow-lg ">
  <h3>AI Prediction Tool</h3>
  <form id="predictForm" autocomplete="off">
    <div class="form-group">
      <label for="StudentExplanation">Student Explanation</label>
      <textarea id="StudentExplanation" name="StudentExplanation" placeholder="Enter student explanation here..." required></textarea>
    </div>
    <div class="form-group">
      <label for="QuestionText">Question Text</label>
      <textarea id="QuestionText" name="QuestionText" placeholder="Enter the question text here..." required></textarea>
    </div>
    <div class="form-group">
      <label for="MC_Answer">MC Answer</label>
      <input type="text" id="MC_Answer" name="MC_Answer" placeholder="Enter multiple choice answer..." required />
    </div>
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-custom">Get Prediction</button>
    </div>
  </form>
</div>

<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true" aria-labelledby="resultModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <h5 class="modal-title" id="resultModalLabel">Prediction Results</h5>
      <div class="modal-body" id="predictionResults"></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('predictForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = {
    samples: [
      {
        row_id: Date.now(),
        StudentExplanation: this.StudentExplanation.value.trim(),
        QuestionText: this.QuestionText.value.trim(),
        MC_Answer: this.MC_Answer.value.trim()
      }
    ]
  };

  fetch("{{ url('/api/predict') }}", {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(formData)
  })
  .then(res => res.json())
  .then(data => {
    let output = '';
    if(data.results && data.results.length > 0) {
      data.results.forEach(item => {
        output += `<div class="mb-3"><strong>Row ID:</strong> ${item.row_id}<br>`;
        output += `<strong>Predictions:</strong><ul>`;
        item.predictions.forEach(pred => {
          output += `<li>${pred}</li>`;
        });
        output += `</ul></div>`;
      });
    } else {
      output = '<p class="text-danger">No predictions returned.</p>';
    }
    document.getElementById('predictionResults').innerHTML = output;
    new bootstrap.Modal(document.getElementById('resultModal')).show();
  })
  .catch(err => {
    document.getElementById('predictionResults').innerHTML = `<p class="text-danger">Error: ${err.message}</p>`;
    new bootstrap.Modal(document.getElementById('resultModal')).show();
  });
});
</script>

</body>
</html>
