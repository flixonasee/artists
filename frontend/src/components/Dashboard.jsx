import React from 'react';

const Dashboard = ({ data }) => {
  return (
    <div className="panel">
      <h2>Activity dashboard</h2>
      <div className="dashboard-cards">
        <div className="card">Artists: {data.stats.artistCount}</div>
        <div className="card">Works: {data.stats.workCount}</div>
        <div className="card">Photos: {data.stats.photoCount}</div>
      </div>
      <h3>Badges</h3>
      <div className="badge-grid">
        {data.badges.map((b) => (
          <div className="card" key={b.id}>
            <strong>{b.name}</strong>
            <p>{b.description}</p>
          </div>
        ))}
      </div>
      <h3>Recent activity</h3>
      <div className="card">
        {data.activity.map((a) => (
          <div key={a.id} className="flex-between" style={{ padding: '6px 0', borderBottom: '1px solid #e2e8f0' }}>
            <span>{a.action}</span>
            <span>{new Date(a.timestamp).toLocaleString()}</span>
          </div>
        ))}
      </div>
      <h3>Ranking</h3>
      <div className="card">
        {data.ranking.map((r) => (
          <div key={r.user_id} className="flex-between" style={{ padding: '6px 0' }}>
            <span>User {r.user_id}</span>
            <strong>{r.points || 0} pts</strong>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Dashboard;
