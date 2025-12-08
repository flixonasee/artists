import fetch from 'node-fetch';

const extractYear = (text) => {
  const match = text.match(/(\d{4})/);
  return match ? parseInt(match[1], 10) : null;
};

export const fetchArtistData = async (name) => {
  try {
    const searchUrl = `https://en.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(name)}`;
    const response = await fetch(searchUrl);
    if (!response.ok) throw new Error('Not found');
    const json = await response.json();
    const birthYear = extractYear(json.extract || '') || extractYear(json.description || '');
    const images = json.thumbnail ? [json.thumbnail.source] : [];
    return {
      birth_year: birthYear,
      bio: json.extract,
      wikipedia_url: json.content_urls?.desktop?.page,
      images,
    };
  } catch (e) {
    return { birth_year: null, bio: '', wikipedia_url: null, images: [] };
  }
};
