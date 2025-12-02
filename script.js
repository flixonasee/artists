const STORAGE_KEY = 'artists-notebook';
const badgeSteps = [
  { threshold: 0, label: 'Baby Curator' },
  { threshold: 50, label: 'Gallery Intern' },
  { threshold: 120, label: 'Emerging Taste-Maker' },
  { threshold: 250, label: 'Institutional Powerhouse' },
];

const state = loadState();

const artistForm = document.querySelector('#artistForm');
const artistList = document.querySelector('#artistList');
const scoreValue = document.querySelector('#scoreValue');
const badgeLabel = document.querySelector('#badgeLabel');

updateScore(0);
renderArtists();

artistForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  const formData = new FormData(artistForm);

  const name = formData.get('name').trim();
  const image = formData.get('image').trim();
  const birthYear = formData.get('birthYear').trim();
  const notes = formData.get('notes').trim();
  const tags = splitAndClean(formData.get('tags'));
  const links = splitAndClean(formData.get('links'));
  const files = formData.getAll('photos');
  const photos = await readFiles(files.filter(Boolean));

  const artist = {
    id: crypto.randomUUID(),
    name,
    image,
    birthYear: birthYear ? Number(birthYear) : null,
    notes,
    tags,
    links,
    photos,
    artworks: [],
  };

  state.artists.unshift(artist);
  const points = calculateArtistPoints(artist);
  updateScore(points);
  saveState();
  renderArtists();
  artistForm.reset();
});

function renderArtists() {
  artistList.innerHTML = '';
  const template = document.querySelector('#artistTemplate');

  state.artists.forEach((artist) => {
    const clone = template.content.cloneNode(true);
    const card = clone.querySelector('.artist-card');

    const imageEl = clone.querySelector('.artist-image');
    imageEl.src = artist.image || 'https://images.unsplash.com/photo-1526313199968-70e399ffe791?auto=format&fit=crop&w=400&q=80';
    imageEl.alt = `${artist.name} portrait`;

    clone.querySelector('.artist-name').textContent = artist.name;
    const meta = artist.birthYear ? `Born ${artist.birthYear}` : 'Year unknown';
    clone.querySelector('.artist-meta').textContent = meta;
    clone.querySelector('.artist-tags').textContent = artist.tags.join(' · ');

    clone.querySelector('.artist-notes').textContent = artist.notes || 'No notes yet — leave space for your impressions.';

    const linksEl = clone.querySelector('.links');
    if (artist.links.length === 0) {
      const placeholder = document.createElement('span');
      placeholder.textContent = 'Add a link when you find one';
      linksEl.append(placeholder);
    } else {
      artist.links.forEach((link) => {
        const chip = document.createElement('a');
        chip.href = formatLink(link);
        chip.target = '_blank';
        chip.rel = 'noreferrer noopener';
        chip.textContent = link;
        linksEl.append(chip);
      });
    }

    const gallery = clone.querySelector('.gallery');
    if (artist.photos.length === 0) {
      const placeholder = document.createElement('span');
      placeholder.className = 'chip';
      placeholder.textContent = 'No photos yet';
      gallery.append(placeholder);
    } else {
      artist.photos.forEach((src) => {
        const img = document.createElement('img');
        img.src = src;
        img.alt = `${artist.name} upload`;
        gallery.append(img);
      });
    }

    const artworksEl = clone.querySelector('.artworks');
    if (artist.artworks.length === 0) {
      const placeholder = document.createElement('p');
      placeholder.className = 'artist-meta';
      placeholder.textContent = 'No artworks yet — add one below.';
      artworksEl.append(placeholder);
    } else {
      artist.artworks.forEach((artwork) => {
        const block = document.createElement('div');
        block.className = 'artwork';
        const title = document.createElement('p');
        title.className = 'artwork-title';
        title.textContent = artwork.title;
        const meta = document.createElement('p');
        meta.className = 'artwork-meta';
        meta.textContent = [artwork.year, artwork.notes].filter(Boolean).join(' · ');
        block.append(title, meta);
        artworksEl.append(block);
      });
    }

    const artworkForm = clone.querySelector('.artwork-form');
    artworkForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const form = event.target;
      const data = new FormData(form);
      const title = data.get('title').trim();
      const year = data.get('year').trim();
      const artworkNotes = data.get('notes').trim();

      const newArtwork = {
        title,
        year: year ? Number(year) : null,
        notes: artworkNotes,
      };

      const index = state.artists.findIndex((item) => item.id === artist.id);
      if (index === -1) return;

      state.artists[index].artworks.unshift(newArtwork);
      const points = 5 + (year ? 1 : 0) + (artworkNotes ? 1 : 0);
      updateScore(points);
      saveState();
      renderArtists();
      form.reset();
    });

    artistList.append(clone);
  });
}

function splitAndClean(input) {
  if (!input) return [];
  return input
    .split(',')
    .map((item) => item.trim())
    .filter(Boolean);
}

function formatLink(link) {
  if (link.startsWith('http://') || link.startsWith('https://')) return link;
  return `https://${link}`;
}

function calculateArtistPoints(artist) {
  let points = 10; // new artist
  if (artist.notes) points += 4;
  if (artist.image) points += 3;
  if (artist.birthYear) points += 2;
  points += artist.tags.length;
  points += artist.links.length;
  points += artist.photos.length * 3;
  return points;
}

function readFiles(files) {
  return Promise.all(
    files.map(
      (file) =>
        new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = () => resolve(reader.result);
          reader.onerror = () => reject(reader.error);
          reader.readAsDataURL(file);
        })
    )
  );
}

function loadState() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return { artists: [], score: 0 };
    return JSON.parse(raw);
  } catch (error) {
    console.warn('Could not load saved artists', error);
    return { artists: [], score: 0 };
  }
}

function saveState() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function updateScore(points) {
  state.score += points;
  scoreValue.textContent = state.score;
  const badge = badgeSteps
    .filter((step) => state.score >= step.threshold)
    .pop();
  badgeLabel.textContent = badge ? badge.label : badgeSteps[0].label;
}
