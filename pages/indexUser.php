<div class="row mt-2">

    <div class="card col-4">
        <div class="card-header bg-primary text-white">AJOUT UTILISATEUR</div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="">Prenom</label>
                    <input type="text" name="prenom" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Nom</label>
                    <input type="text" name="nom" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Login</label>
                    <input type="text" name="login" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Mot de passe</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Role</label>
                    <select name="role" class="form-control" id="">
                        <option value="admin">Administrateur</option>
                        <option value="user">User Simple</option>
                    </select>
                </div>

                <button class="btn btn-primary mt-3">Enregistrer</button>
            </form>
        </div>
    </div>

    <div class="card col">
        <div class="card-header bg-primary text-white text-center">LISTE DES UTILISATEURS</div>
        <div class="card-body">
           <table class="table table-bordered">
                <tr>
                    <th>#</th>
                    <th>Prenom</th>
                    <th>Nom</th>
                    <th>Login</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
           </table>
        </div>
    </div>
</div>