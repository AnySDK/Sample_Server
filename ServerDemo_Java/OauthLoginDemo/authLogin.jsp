<%@ page language="java" contentType="text/html; charset=utf-8"
    pageEncoding="utf-8"%>
<%@ page import="com.anysdk.auth.Login" %>
<%
	Login login = new Login();
	login.check( request, response );
%>
