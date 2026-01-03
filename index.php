<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ouverture des locaux</title>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <style>
    body { font-family: Arial; padding: 20px; max-width: 400px; margin: auto; }
    button { width: 100%; padding: 12px; margin-top: 10px; }
    select, input { width: 100%; padding: 10px; margin-top: 10px; }
  </style>
</head>
<body>

<h2>Connexion</h2>
<input id="email" type="email" placeholder="Email">
<input id="password" type="password" placeholder="Mot de passe">
<button onclick="login()">Se connecter</button>

<div id="app" style="display:none;">
  <h2>Gestion du local</h2>
  <select id="localSelect"></select>
  <button onclick="ouvrir()">Ouvrir</button>
  <button onclick="fermer()">Fermer</button>
</div>

<script>
const supabase = supabase.createClient(
  "https://TON_PROJET.supabase.co",
  "TA_CLE_ANON"
);

async function login() {
  const { data, error } = await supabase.auth.signInWithPassword({
    email: email.value,
    password: password.value
  });
  if (error) return alert(error.message);
  document.getElementById('app').style.display = 'block';
  loadLocaux();
}

async function loadLocaux() {
  const { data } = await supabase.from('locaux').select('*');
  const select = document.getElementById('localSelect');
  data.forEach(l => {
    const opt = document.createElement('option');
    opt.value = l.id;
    opt.textContent = l.nom;
    select.appendChild(opt);
  });
}

async function ouvrir() {
  await enregistrer('ouverture');
}
async function fermer() {
  await enregistrer('fermeture');
}

async function enregistrer(type) {
  const user = (await supabase.auth.getUser()).data.user;
  const localId = localSelect.value;

  const { data: emp } = await supabase
    .from('employes')
    .select('id')
    .eq('auth_id', user.id)
    .single();

  const { error } = await supabase
    .from('ouvertures')
    .insert({
      local_id: localId,
      employe_id: emp.id,
      type: type
    });

  if (error) alert(error.message);
  else alert(type + " enregistrée");
}
</script>

</body>
</html>