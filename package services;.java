package services;

import entities.User;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import org.mindrot.jbcrypt.BCrypt;
import utils.MyDB;
import services.PasswordEncoder;

public class UserService implements IService<User> {

    Connection cnx;

    public UserService() {
        cnx = MyDB.getInstance().getCnx();
    }

    @Override
    public void ajouter(User t) throws SQLException {
        String encodedPassword = PasswordEncoder.encode(t.getPassword());

//    String req = "INSERT INTO user(username, roles, password, nom, prenom, email, reset_token, status) "
//               + "VALUES('" + t.getUsername() + "', '[\"ROLE_USER\"]', '" + t.getPassword() + "', '" + t.getNom() + "', '" + t.getPrenom() + "', '" + t.getEmail() + "', NULL, 'enabled')";
        String req = "INSERT INTO user(username, roles, password, nom, prenom, email, reset_token, status) "
                + "VALUES('" + t.getUsername() + "', '[\"ROLE_USER\"]', '" + encodedPassword + "', '" + t.getNom() + "', '" + t.getPrenom() + "', '" + t.getEmail() + "', NULL, 'enabled')";
        Statement st = cnx.createStatement();
        st.executeUpdate(req);
    }

    @Override
    public void modifier(User t) throws SQLException {
        String req = "update user set nom = ?, prenom = ?, email = ?, username = ? where id = ?";
        PreparedStatement ps = cnx.prepareStatement(req);
        ps.setString(1, t.getNom());
        ps.setString(2, t.getPrenom());
        ps.setString(3, t.getEmail());
        ps.setString(4, t.getUsername());
        ps.setInt(5, t.getId());
        ps.executeUpdate();
    }

    @Override
    public void supprimer(User t) throws SQLException {
        String req = "delete from user where id = ?";
        PreparedStatement ps = cnx.prepareStatement(req);
        ps.setInt(1, t.getId());
        ps.executeUpdate();
    }

    @Override
    public List<User> recuperer() throws SQLException {
        List<User> users = new ArrayList<>();
        String req = "select * from user";
        Statement st = cnx.createStatement();
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            User u = new User();
            u.setUsername(rs.getString("username"));
            u.setId(rs.getInt("id"));
            u.setNom(rs.getString("nom"));
            u.setPrenom(rs.getString("prenom"));
            u.setStatus(rs.getString("status"));
            u.setEmail(rs.getString("email"));
            u.setPassword(rs.getString("password"));
            users.add(u);
        }
        return users;
    }

    public List<User> recupererById(int id) throws SQLException {
        List<User> users = new ArrayList<>();
        String req = "select * from user where id= ?";
        PreparedStatement st = cnx.prepareStatement(req);
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            User u = new User();
            u.setId(rs.getInt("id"));
            u.setNom(rs.getString("nom"));
            u.setPrenom(rs.getString("prenom"));
            u.setEmail(rs.getString("email"));
            u.setPassword(rs.getString("password"));
            users.add(u);
        }
        return users;
    }

    public User login(String username, String password) throws SQLException {
        String req = "SELECT * FROM user WHERE username = ?";
        PreparedStatement ps = cnx.prepareStatement(req);
        ps.setString(1, username);
        ResultSet rs = ps.executeQuery();
        if (rs.next()) {
            
            String encodedPassword = rs.getString("password");
            String encodedPassword1 = "$2a$" + encodedPassword.substring(4);
            boolean passwordMatch = BCrypt.checkpw(password, encodedPassword1);
            if (passwordMatch) {
                int id = rs.getInt("id");
                String nom = rs.getString("nom");
                String email = rs.getString("email");
                String prenom = rs.getString("prenom");
                String status = rs.getString("status");
                String reset_token = rs.getString("reset_token");
                String[] roles = rs.getString("roles").split(",");
                User user = new User(id, username, encodedPassword, email, nom, prenom, status, reset_token, roles);
                return user;
            } else {
                // Login failed, return null
                return null;
            }
        } else {
            // Login failed, return null
            return null;
        }
    }

    public boolean emailExists(String email) throws SQLException {
        String req = "SELECT * FROM user WHERE email = ?";
        PreparedStatement ps = cnx.prepareStatement(req);
        ps.setString(1, email);
        ResultSet rs = ps.executeQuery();
        if (rs.next()) {
            // Email exists in the database, return true
            return true;
        } else {
            // Email does not exist in the database, return false
            return false;
        }
    }

    public User getUserByUsername(String username) throws SQLException {
        User user = null;
        String query = "SELECT * FROM user WHERE username = ?";
        PreparedStatement statement = cnx.prepareStatement(query);
        statement.setString(1, username);
        ResultSet result = statement.executeQuery();

        if (result.next()) {
            int id = result.getInt("id");
            String password = result.getString("password");
            String email = result.getString("email");
            String nom = result.getString("nom");
            String prenom = result.getString("prenom");
            String status = result.getString("status");
            String resetToken = result.getString("reset_token");
            String[] roles = result.getString("roles").split(",");

            user = new User(id, username, password, email, nom, prenom, status, resetToken, roles);
        }

        return user;
    }

    public boolean checkUsernameExists(String username, int userId) throws SQLException {
        String query = "SELECT id FROM user WHERE username = ? AND id != ?";
        PreparedStatement statement = cnx.prepareStatement(query);
        statement.setString(1, username);
        statement.setInt(2, userId);
        ResultSet resultSet = statement.executeQuery();
        boolean exists = resultSet.next();
        resultSet.close();
        statement.close();
        return exists;
    }

    public void aaaa(int id) throws SQLException {
        String query = "DELETE FROM user WHERE id = ?";
        PreparedStatement stmt = cnx.prepareStatement(query);
        stmt.setInt(1, id);
        stmt.executeUpdate();
    }
    
    public int ChercherMail(String email) {

        try {
            String req = "SELECT * from `user` WHERE `user`.`email` ='" + email + "'  ";
            Statement st = cnx.createStatement();
            ResultSet rs = st.executeQuery(req);
            while (rs.next()) {
                if (rs.getString("email").equals(email)) {
                    System.out.println("mail trouvé ! ");
                    return 1;
                }
            }
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
        return -1;
    }
    
    public void ResetPaswword(String email, String password) {
        try {

            String req = "UPDATE user SET password = ? WHERE email = ?";
            PreparedStatement ps = cnx.prepareStatement(req);

            ps.setString(1, password);
            ps.setString(2, email);

            ps.executeUpdate();
            System.out.println("Password updated !");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }

    }
}