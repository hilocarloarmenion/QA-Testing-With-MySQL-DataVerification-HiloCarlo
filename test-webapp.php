<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "birthday_wishnote";
$confirmation_message = "";
$message_type = "success";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = trim($_POST['FirstName'] ?? '');
        $lastName = trim($_POST['LastName'] ?? '');
        $govPosition = trim($_POST['GovPosition'] ?? '');
        $location = trim($_POST['Location'] ?? '');
        if ($firstName === '' || $lastName === '' || $govPosition === '' ||
            mb_strlen($firstName) > 30 || mb_strlen($lastName) > 30 ||
            mb_strlen($govPosition) > 20 || mb_strlen($location) > 40) {
            $confirmation_message = 'Please enter valid data within the field limits.';
            $message_type = 'error';
        } else {
            // INTENTIONAL DEFECT FOR QA TESTING: Location is captured but omitted
            // from both INSERT column list and bind_param. Do not fix for this exercise.
            $sql = 'INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition) VALUES (?, ?, ?)';
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $confirmation_message = 'Error: Could not prepare the database query.';
                $message_type = 'error';
            } else {
                $stmt->bind_param('sss', $firstName, $lastName, $govPosition);
                $stmt->execute();
                $confirmation_message = 'Success! The wish note has been sent.';
                $stmt->close();
            }
        }
    }
    $conn->close();
} catch (mysqli_sql_exception $e) {
    error_log('Wish Note database error: ' . $e->getMessage());
    $confirmation_message = 'Database error. Please check the local database configuration.';
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kara David's Wish Note</title>
<style>
:root{font-family:system-ui,-apple-system,Segoe UI,sans-serif;color:#183153;background:#f2f5f9}
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px}
.card{width:min(100%,520px);background:white;padding:32px;border-radius:16px;box-shadow:0 12px 36px #18315318}
h2{margin:0 0 8px;color:#1859a6}p.sub{color:#65758b;margin:0 0 26px}
label{display:block;margin:16px 0 7px;font-weight:600;font-size:14px}
input{width:100%;padding:12px;border:1px solid #c9d5e4;border-radius:8px;font:inherit}
input:focus{outline:2px solid #74aeec;outline-offset:1px}
button{font:inherit;cursor:pointer;border:0;border-radius:8px;padding:12px 18px;font-weight:650}
.primary{background:#2167b5;color:white}.primary:hover{background:#144d90}
.secondary{background:#e8eef6;color:#193e6b}.secondary:hover{background:#d4e2f2}
.submit{width:100%;margin-top:24px}.notice{padding:12px 14px;border-radius:8px;margin:0 0 18px;background:#e7f6ec;color:#175e31}
.notice.error{background:#fcebea;color:#922e2e}
.overlay{position:fixed;inset:0;background:#10253d9c;display:none;align-items:center;justify-content:center;padding:18px}
.overlay.open{display:flex}.dialog{background:white;border-radius:14px;padding:25px;width:min(100%,450px);box-shadow:0 20px 70px #0003}
.dialog h3{margin-top:0}.summary{margin:18px 0}.summary div{padding:8px 0;border-bottom:1px solid #e8edf3;overflow-wrap:anywhere}
.actions{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap}
@media(max-width:400px){.card{padding:22px}.actions button{flex:1}}
</style>
</head>
<body>
<main class="card">
<h2>Kara David's Wish Note</h2>
<p class="sub">Enter the target details and verify them before sending.</p>
<?php if ($confirmation_message !== ''): ?>
<div role="status" class="notice <?= $message_type === 'error' ? 'error' : '' ?>"><?= htmlspecialchars($confirmation_message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<form id="wishForm" method="post">
<label for="first">Target's First Name</label><input id="first" name="FirstName" maxlength="30" required>
<label for="last">Target's Last Name</label><input id="last" name="LastName" maxlength="30" required>
<label for="position">Target's Government Position</label><input id="position" name="GovPosition" maxlength="20" required>
<label for="location">Target's Location</label><input id="location" name="Location" maxlength="40">
<button type="submit" class="primary submit">Send Wish Note</button>
</form>
</main>
<div id="modal" class="overlay" role="presentation">
<div class="dialog" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
<h3 id="modalTitle">Verify Target Data</h3>
<p>Please confirm these details before sending.</p>
<div id="summary" class="summary"></div>
<div class="actions"><button type="button" id="cancel" class="secondary">Cancel</button><button type="button" id="confirm" class="primary">Confirm &amp; Send</button></div>
</div>
</div>
<script>
const form=document.getElementById('wishForm');
const modal=document.getElementById('modal');
const summary=document.getElementById('summary');
const cancel=document.getElementById('cancel');
const confirmButton=document.getElementById('confirm');
form.addEventListener('submit',function(e){
  e.preventDefault();
  if(!form.reportValidity())return;
  summary.replaceChildren();
  for(const [label,name] of [['First Name','FirstName'],['Last Name','LastName'],['Government Position','GovPosition'],['Location','Location']]){
    const row=document.createElement('div');
    const strong=document.createElement('strong');strong.textContent=label+': ';
    row.append(strong,document.createTextNode(form.elements[name].value||'(not provided)'));
    summary.append(row);
  }
  modal.classList.add('open');confirmButton.focus();
});
function closeModal(){modal.classList.remove('open');form.querySelector('.submit').focus()}
cancel.addEventListener('click',closeModal);
modal.addEventListener('click',e=>{if(e.target===modal)closeModal()});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal.classList.contains('open'))closeModal()});
confirmButton.addEventListener('click',()=>form.submit());
</script>
</body>
</html>
