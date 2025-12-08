import React, { useEffect } from 'react';

const PhotoLightbox = ({ src, onClose }) => {
  useEffect(() => {
    const handler = (e) => {
      if (e.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', handler);
    return () => window.removeEventListener('keydown', handler);
  }, [onClose]);
  return (
    <div className="lightbox" onClick={onClose}>
      <img src={src} alt="zoom" />
    </div>
  );
};

export default PhotoLightbox;
