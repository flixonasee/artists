import React, { useMemo, useState } from 'react';
import InlineEdit from './InlineEdit.jsx';
import PhotoLightbox from './PhotoLightbox.jsx';
import { compressImage } from '../lib/image.js';

const Section = ({ title, children }) => {
  const [open, setOpen] = useState(true);
  return (
    <div className="section">
      <header onClick={() => setOpen(!open)}>
        <h3>{title}</h3>
        <span>{open ? '−' : '+'}</span>
      </header>
      {open && <div>{children}</div>}
    </div>
  );
};

const ArtistDetail = ({ artist, onUpdate, onAddPhoto, onAddWork }) => {
  const [lightbox, setLightbox] = useState(null);
  const updateField = (field, value) => onUpdate(artist.id, { [field]: value });

  const handlePhotoUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const compressed = await compressImage(file);
    await onAddPhoto(artist.id, compressed, '');
  };

  const handleAddWork = () => {
    const title = prompt('Work title');
    if (!title) return;
    const year = prompt('Year (optional)');
    onAddWork(artist.id, { title, year: year ? parseInt(year, 10) : null, notes: '' });
  };

  const tagString = useMemo(() => artist.tags?.join(', ') || '', [artist.tags]);

  return (
    <div className="panel" style={{ overflowY: 'auto' }}>
      <h2>
        <InlineEdit value={artist.name} onSave={(v) => updateField('name', v)} />
      </h2>
      <Section title="Basics">
        <div className="flex-between">
          <label>Birth year</label>
          <InlineEdit value={artist.birth_year || ''} onSave={(v) => updateField('birth_year', parseInt(v, 10) || null)} />
        </div>
        <div>
          <label>Bio</label>
          <textarea
            className="inline-input"
            style={{ minHeight: 80 }}
            defaultValue={artist.bio}
            onBlur={(e) => updateField('bio', e.target.value)}
          />
        </div>
      </Section>
      <Section title="Tags & Links">
        <InlineEdit
          value={tagString}
          placeholder="tag1, tag2"
          onSave={(v) => updateField('tags', v.split(',').map((t) => t.trim()).filter(Boolean))}
        />
        <InlineEdit
          value={(artist.links || []).join(', ')}
          placeholder="links separated by comma"
          onSave={(v) => updateField('links', v.split(',').map((t) => t.trim()).filter(Boolean))}
        />
      </Section>
      <Section title="Featured image">
        {artist.featured_image_url && (
          <img src={artist.featured_image_url} alt="featured" style={{ width: '100%', borderRadius: 12 }} />
        )}
      </Section>
      <Section title="Photos">
        <input type="file" accept="image/*" onChange={handlePhotoUpload} />
        <div className="gallery">
          {(artist.photos || []).map((p) => (
            <div key={p.id} className="card" onClick={() => setLightbox(p.image_url)}>
              <img src={p.image_url} alt={p.caption} style={{ width: '100%', borderRadius: 8 }} />
              <div>{p.caption}</div>
            </div>
          ))}
        </div>
      </Section>
      <Section title="Works">
        <button onClick={handleAddWork}>Add work</button>
        {(artist.works || []).map((w) => (
          <div key={w.id} className="card">
            <strong>{w.title}</strong> {w.year && <span>({w.year})</span>}
            <div>{w.notes}</div>
          </div>
        ))}
      </Section>
      {lightbox && <PhotoLightbox src={lightbox} onClose={() => setLightbox(null)} />}
    </div>
  );
};

export default ArtistDetail;
