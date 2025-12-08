import React, { useState } from 'react';

const InlineEdit = ({ value, onSave, placeholder }) => {
  const [draft, setDraft] = useState(value || '');
  return (
    <input
      className="inline-input"
      value={draft}
      placeholder={placeholder}
      onChange={(e) => setDraft(e.target.value)}
      onBlur={() => onSave(draft)}
    />
  );
};

export default InlineEdit;
