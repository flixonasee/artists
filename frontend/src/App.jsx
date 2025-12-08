import React, { useEffect, useMemo, useState } from 'react';
import axios from 'axios';
import Fuse from 'fuse.js';
import Dashboard from './components/Dashboard.jsx';
import ArtistList from './components/ArtistList.jsx';
import ArtistDetail from './components/ArtistDetail.jsx';
import LoginForm from './components/LoginForm.jsx';

const api = axios.create({ baseURL: '/api' });

const App = () => {
  const [token, setToken] = useState(localStorage.getItem('token'));
  const [user, setUser] = useState(null);
  const [artists, setArtists] = useState([]);
  const [selected, setSelected] = useState(null);
  const [view, setView] = useState('dashboard');
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    if (token) {
      api.defaults.headers.common.Authorization = `Bearer ${token}`;
      fetchArtists();
      fetchDashboard();
    }
  }, [token]);

  const [dashboard, setDashboard] = useState(null);

  const fetchDashboard = async () => {
    const res = await api.get('/dashboard');
    setDashboard(res.data);
  };

  const fetchArtists = async () => {
    const res = await api.get('/artists');
    setArtists(res.data);
  };

  const handleLogin = async (email, password) => {
    const res = await api.post('/login', { email, password });
    setToken(res.data.token);
    localStorage.setItem('token', res.data.token);
    setUser(res.data.user);
  };

  const fuse = useMemo(() => new Fuse(artists, { keys: ['name', 'tags'], threshold: 0.3 }), [artists]);
  const filteredArtists = searchTerm ? fuse.search(searchTerm).map((r) => r.item) : artists;

  const addArtist = async (data) => {
    const res = await api.post('/artists', data);
    setArtists([res.data.artist, ...artists]);
    setSelected(res.data.artist.id);
    fetchDashboard();
  };

  const updateArtist = async (id, data) => {
    const res = await api.patch(`/artists/${id}`, data);
    setArtists(artists.map((a) => (a.id === id ? res.data : a)));
    fetchDashboard();
  };

  const addPhoto = async (artistId, file, caption) => {
    const form = new FormData();
    form.append('file', file);
    const upload = await api.post('/upload', form, { headers: { 'Content-Type': 'multipart/form-data' } });
    const photoRes = await api.post(`/artists/${artistId}/photos`, { image_url: upload.data.url, caption });
    setArtists(
      artists.map((a) => (a.id === artistId ? { ...a, photos: [photoRes.data, ...(a.photos || [])] } : a))
    );
    fetchDashboard();
    return photoRes.data;
  };

  const addWork = async (artistId, work) => {
    const res = await api.post(`/artists/${artistId}/works`, work);
    setArtists(artists.map((a) => (a.id === artistId ? { ...a, works: [res.data, ...(a.works || [])] } : a)));
    fetchDashboard();
  };

  if (!token) {
    return <LoginForm onLogin={handleLogin} />;
  }

  return (
    <div className="app-shell">
      <header className="app-header">
        <div className="logo">Artist Index</div>
        <nav>
          <button className={view === 'dashboard' ? 'active' : ''} onClick={() => setView('dashboard')}>
            Dashboard
          </button>
          <button className={view === 'artists' ? 'active' : ''} onClick={() => setView('artists')}>
            Artists
          </button>
        </nav>
        <div className="user-info">{user?.name || 'Logged in'}</div>
      </header>
      <main>
        {view === 'dashboard' && dashboard && <Dashboard data={dashboard} />}
        {view === 'artists' && (
          <div className="layout">
            <ArtistList
              artists={filteredArtists}
              onSelect={(id) => setSelected(id)}
              onAdd={addArtist}
              searchTerm={searchTerm}
              setSearchTerm={setSearchTerm}
            />
            {selected && (
              <ArtistDetail
                key={selected}
                artist={artists.find((a) => a.id === selected)}
                onUpdate={updateArtist}
                onAddPhoto={addPhoto}
                onAddWork={addWork}
              />
            )}
          </div>
        )}
      </main>
    </div>
  );
};

export default App;
