import {
  listArtists,
  createArtist,
  updateArtist,
  deleteArtist,
  getArtist,
} from '../models/artistModel.js';
import { createPhoto, getPhotos } from '../models/photoModel.js';
import { createWork, getWorks } from '../models/workModel.js';
import { fetchArtistData } from '../services/wikipediaService.js';
import { awardForAction } from '../services/gamificationService.js';

export const getAllArtists = (req, res) => {
  const artists = listArtists().map((a) => ({
    ...a,
    photos: getPhotos(a.id),
    works: getWorks(a.id),
  }));
  res.json(artists);
};

export const postArtist = async (req, res) => {
  const { name, tags, links, featured_image_url } = req.body;
  if (!name) return res.status(400).json({ error: 'Name required' });
  const wiki = await fetchArtistData(name);
  const artist = createArtist({
    name,
    birth_year: wiki.birth_year,
    bio: wiki.bio,
    featured_image_url: featured_image_url || wiki.images[0],
    tags: tags || [],
    links: links || (wiki.wikipedia_url ? [wiki.wikipedia_url] : []),
  });
  awardForAction(req.user.id, artist.id, 'create_artist');
  res.status(201).json({ artist, wikipedia: wiki });
};

export const patchArtist = (req, res) => {
  const artist = updateArtist(parseInt(req.params.id, 10), req.body);
  if (!artist) return res.status(404).json({ error: 'Not found' });
  awardForAction(req.user.id, artist.id, 'edit_artist');
  res.json(artist);
};

export const deleteArtistHandler = (req, res) => {
  deleteArtist(parseInt(req.params.id, 10));
  res.status(204).end();
};

export const postPhoto = (req, res) => {
  const artistId = parseInt(req.params.id, 10);
  const photo = createPhoto(artistId, { image_url: req.body.image_url, caption: req.body.caption });
  awardForAction(req.user.id, artistId, 'upload_photo');
  res.status(201).json(photo);
};

export const postWork = (req, res) => {
  const artistId = parseInt(req.params.id, 10);
  const { title, year, image_url, notes } = req.body;
  if (!title) return res.status(400).json({ error: 'Title required' });
  const work = createWork(artistId, { title, year, image_url, notes });
  awardForAction(req.user.id, artistId, 'add_work');
  res.status(201).json(work);
};

export const getArtistDetail = (req, res) => {
  const artist = getArtist(parseInt(req.params.id, 10));
  if (!artist) return res.status(404).json({ error: 'Not found' });
  artist.photos = getPhotos(artist.id);
  artist.works = getWorks(artist.id);
  res.json(artist);
};
