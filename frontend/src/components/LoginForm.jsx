import React, { useState } from 'react';

const LoginForm = ({ onLogin }) => {
  const [email, setEmail] = useState('gianmaria@example.com');
  const [password, setPassword] = useState('password123');
  const handleSubmit = (e) => {
    e.preventDefault();
    onLogin(email, password);
  };
  return (
    <div className="login-card">
      <h2>Artist Index Login</h2>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Email</label>
          <input value={email} onChange={(e) => setEmail(e.target.value)} className="inline-input" />
        </div>
        <div>
          <label>Password</label>
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="inline-input" />
        </div>
        <button style={{ marginTop: 12 }}>Login</button>
      </form>
    </div>
  );
};

export default LoginForm;
