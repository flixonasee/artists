import React, { useState } from 'react';

const ArtistList = ({ artists, onSelect, onAdd, searchTerm, setSearchTerm }) => {
  const [newName, setNewName] = useState('');
  const handleAdd = () => {
    if (!newName) return;
    onAdd({ name: newName, tags: [], links: [] });
    setNewName('');
  };
  return (
    <div className="panel">
      <div className="list-search">
        <input
          className="inline-input"
          placeholder="Search artists"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
        />
      </div>
      <div>
        {artists.map((a) => (
          <div className="list-item" key={a.id} onClick={() => onSelect(a.id)}>
            <strong>{a.name}</strong>
            <div style={{ fontSize: 12, color: '#475569' }}>{a.tags?.join(', ')}</div>
          </div>
        ))}
      </div>
      <div style={{ marginTop: 20 }}>
        <input
          className="inline-input"
          placeholder="Add artist"
          value={newName}
          onChange={(e) => setNewName(e.target.value)}
        />
        <button onClick={handleAdd} style={{ marginTop: 8 }}>
          Create
        </button>
      </div>
    </div>
  );
};

export default ArtistList;
