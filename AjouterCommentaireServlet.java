package com.vdece;

import java.io.*;
import java.sql.*;
import javax.servlet.*;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;

@WebServlet("/ajouter_commentaire")
public class AjouterCommentaireServlet extends HttpServlet {

    @Override
    protected void doOptions(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        resp.setHeader("Access-Control-Allow-Origin", "http://localhost");
        resp.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
        resp.setHeader("Access-Control-Allow-Headers", "Content-Type");
        resp.setStatus(HttpServletResponse.SC_OK);
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        request.setCharacterEncoding("UTF-8");
        response.setContentType("text/html; charset=UTF-8");
        response.setHeader("Access-Control-Allow-Origin", "http://localhost");
        response.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
        response.setHeader("Access-Control-Allow-Headers", "Content-Type");

        response.setHeader("Access-Control-Allow-Origin", "http://localhost");

        String pseudo = request.getParameter("pseudo");
        String comment = request.getParameter("comment");
        int vde_id = Integer.parseInt(request.getParameter("vde_id"));

        System.out.println("Requête ajout commentaire reçue");
        System.out.println("Pseudo: " + pseudo);
        System.out.println("Comment: " + comment);
        System.out.println("Vde_id: " + vde_id);

        HttpSession session = request.getSession();
        session.setAttribute("pseudo", pseudo);

        try (Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/viedece", "root", "");
             PreparedStatement stmt = conn.prepareStatement("INSERT INTO comments (vde_id, pseudo, comment) VALUES (?, ?, ?)");
             PreparedStatement select = conn.prepareStatement("SELECT * FROM comments WHERE vde_id = ? ORDER BY date DESC")) {

            stmt.setInt(1, vde_id);
            stmt.setString(2, pseudo);
            stmt.setString(3, comment);
            stmt.executeUpdate();

            select.setInt(1, vde_id);
            ResultSet rs = select.executeQuery();

            StringBuilder output = new StringBuilder();
            while (rs.next()) {
                output.append("<div class='alert alert-light border fade-in'>")
                      .append("<strong>").append(escape(rs.getString("pseudo"))).append("</strong><br>")
                      .append("<small class='text-muted'>").append(rs.getString("date")).append("</small>")
                      .append("<p>").append(escape(rs.getString("comment")).replace("\n", "<br>")).append("</p>")
                      .append("</div>");
            }

            response.getWriter().write(output.toString());

        } catch (Exception e) {
            response.getWriter().write("Erreur : " + e.getMessage());
            e.printStackTrace();
        }
    }
    @Override
protected void doOptions(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
    resp.setHeader("Access-Control-Allow-Origin", "http://localhost");
    resp.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
    resp.setHeader("Access-Control-Allow-Headers", "Content-Type");
    resp.setStatus(HttpServletResponse.SC_OK);
}


    private String escape(String str) {
        if (str == null) return "";
        return str.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
                  .replace("\"", "&quot;").replace("'", "&#x27;");
    }
}
