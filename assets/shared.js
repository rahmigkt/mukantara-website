// MUKANTARA — ortak Supabase istemcisi ve yardımcı fonksiyonlar
const SUPABASE_URL = "https://xcsvobtewvjlnjeyqiux.supabase.co";
const SUPABASE_KEY = "sb_publishable_yjwxxm5IaMkEi1TCugejzA__cqLpYnP";

const sb = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

function escapeHtml(str) {
  if (str == null) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function publicImageUrl(path) {
  if (!path) return null;
  if (path.startsWith("http")) return path;
  const { data } = sb.storage.from("mukantara-images").getPublicUrl(path);
  return data.publicUrl;
}

function publicVideoUrl(path) {
  if (!path) return null;
  if (path.startsWith("http")) return path;
  const { data } = sb.storage.from("mukantara-videos").getPublicUrl(path);
  return data.publicUrl;
}

function videoMimeType(url) {
  const ext = (url.split('.').pop() || '').toLowerCase().split('?')[0];
  const map = { mp4:'video/mp4', mov:'video/quicktime', webm:'video/webm', ogg:'video/ogg', m4v:'video/mp4' };
  return map[ext] || '';
}

// Kategori ikonları (örnek görsel yoksa yedek olarak kullanılır)
const CATEGORY_ICONS = {
  "anitsal-eserler": '<svg viewBox="0 0 60 60"><path d="M30 8 L48 20 L48 40 L30 52 L12 40 L12 20 Z"/><circle cx="30" cy="30" r="8"/></svg>',
  "muze-standartlarinda-eserler": '<svg viewBox="0 0 60 60"><circle cx="30" cy="30" r="18"/><line x1="30" y1="12" x2="30" y2="48"/><line x1="12" y1="30" x2="48" y2="30"/></svg>',
  "ihtisas-hediyelikleri": '<svg viewBox="0 0 60 60"><polygon points="30,8 48,20 48,40 30,52 12,40 12,20"/></svg>',
  "default": '<svg viewBox="0 0 60 60"><polygon points="30,8 48,20 48,40 30,52 12,40 12,20"/></svg>'
};
function categoryIcon(slug) {
  return CATEGORY_ICONS[slug] || CATEGORY_ICONS["default"];
}
