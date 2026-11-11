import { describe, it, expect } from 'vitest';

describe('Interactive Diagram Hotspots Logic (DEC-02)', () => {
  const getPartCode = (part) => {
    return part?.phu_tung?.ma_phu_tung ? part.phu_tung.ma_phu_tung.toUpperCase() : '';
  };

  const getHotspotX = (part) => {
    if (part.x !== null && part.x !== undefined && part.x !== '') return parseFloat(part.x);
    if (part.vi_tri_x !== null && part.vi_tri_x !== undefined && part.vi_tri_x !== '') return parseFloat(part.vi_tri_x);
    return null;
  };

  const getHotspotY = (part) => {
    if (part.y !== null && part.y !== undefined && part.y !== '') return parseFloat(part.y);
    if (part.vi_tri_y !== null && part.vi_tri_y !== undefined && part.vi_tri_y !== '') return parseFloat(part.vi_tri_y);
    return null;
  };

  const isPartSelected = (part, selectedCode, targetCode) => {
    const code = getPartCode(part);
    if (!code) return false;
    if (selectedCode && selectedCode === code) return true;
    if (targetCode && targetCode === code) return true;
    return false;
  };

  it('correctly parses hotspot percentage coordinates from x/y and vi_tri_x/vi_tri_y', () => {
    const part1 = { id: 1, ref_number: '12', x: 45.5, y: 72.8, phu_tung: { ma_phu_tung: '69M-E1111-00' } };
    const part2 = { id: 2, ref_number: '15', vi_tri_x: '30.2', vi_tri_y: '55.1', phu_tung: { ma_phu_tung: '69M-E1112-00' } };
    const part3 = { id: 3, ref_number: '20', x: null, y: null, phu_tung: { ma_phu_tung: '69M-E1113-00' } };

    expect(getHotspotX(part1)).toBe(45.5);
    expect(getHotspotY(part1)).toBe(72.8);
    expect(getHotspotX(part2)).toBe(30.2);
    expect(getHotspotY(part2)).toBe(55.1);
    expect(getHotspotX(part3)).toBeNull();
    expect(getHotspotY(part3)).toBeNull();
  });

  it('evaluates two-way selected status accurately', () => {
    const part = { id: 1, ref_number: '5', phu_tung: { ma_phu_tung: '67D-12411-00' } };

    expect(isPartSelected(part, '67D-12411-00', '')).toBe(true);
    expect(isPartSelected(part, '', '67D-12411-00')).toBe(true);
    expect(isPartSelected(part, 'OTHER-CODE', '')).toBe(false);
  });
});
