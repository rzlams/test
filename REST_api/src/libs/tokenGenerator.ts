const tokenGenerator = (num) => {
  const chars =
  '1234567890';
  let result = '';
  for (let i = 0; i < num; i++) {
    const at = Math.floor(Math.random() * (chars.length + 1));
    result += chars[at];
  }

  return result;
};


module.exports = tokenGenerator;
