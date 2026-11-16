"""Builds languages/degriz-euguarantee.pot, -sl_SI.po and -sl_SI.mo from the PHP sources (no gettext tools needed).

Usage: python tools/build-i18n.py   (translations live in tools/translations.json)
"""
import glob
import json
import os
import re
import struct

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DOMAIN = 'degriz-euguarantee'
TRANSLATIONS = json.load(open(os.path.join(ROOT, 'tools', 'translations.json'), encoding='utf-8'))


def collect():
    found = {}
    pattern = re.compile(r"(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'" + DOMAIN + "'")
    for path in glob.glob(os.path.join(ROOT, '**', '*.php'), recursive=True):
        if os.sep + 'vendor' + os.sep in path or os.sep + 'tests' + os.sep in path:
            continue
        rel = os.path.relpath(path, ROOT).replace(os.sep, '/')
        text = open(path, encoding='utf-8').read()
        for m in pattern.finditer(text):
            msgid = m.group(1).replace("\\'", "'")
            line = text.count('\n', 0, m.start()) + 1
            found.setdefault(msgid, []).append(f'{rel}:{line}')
    return found


def esc(s):
    return s.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n')


def write_po(path, found, translated):
    header = (
        'msgid ""\nmsgstr ""\n'
        '"Project-Id-Version: Degriz EU Guarantee 1.0\\n"\n'
        '"Content-Type: text/plain; charset=UTF-8\\n"\n'
        '"Content-Transfer-Encoding: 8bit\\n"\n'
        + ('"Language: sl_SI\\n"\n"Plural-Forms: nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3);\\n"\n' if translated else '"Language: \\n"\n')
    )
    out = [header]
    for msgid, refs in sorted(found.items()):
        out.append('#: ' + ' '.join(refs) + '\nmsgid "' + esc(msgid) + '"\nmsgstr "' + (esc(TRANSLATIONS[msgid]) if translated else '') + '"\n')
    open(path, 'w', encoding='utf-8', newline='\n').write('\n'.join(out))


def write_mo(path, pairs):
    pairs = sorted(pairs.items())
    ids = b''
    strs = b''
    offsets = []
    for k, v in pairs:
        kb, vb = k.encode('utf-8'), v.encode('utf-8')
        offsets.append((len(ids), len(kb), len(strs), len(vb)))
        ids += kb + b'\0'
        strs += vb + b'\0'
    n = len(pairs)
    keystart = 7 * 4 + 16 * n
    valuestart = keystart + len(ids)
    koffsets, voffsets = [], []
    for o1, l1, o2, l2 in offsets:
        koffsets += [l1, o1 + keystart]
        voffsets += [l2, o2 + valuestart]
    data = struct.pack('Iiiiiii', 0x950412DE, 0, n, 7 * 4, 7 * 4 + n * 8, 0, 0)
    data += struct.pack('%di' % len(koffsets + voffsets), *(koffsets + voffsets))
    data += ids + strs
    open(path, 'wb').write(data)


def main():
    found = collect()
    missing = [m for m in found if m not in TRANSLATIONS]
    if missing:
        raise SystemExit('Missing Slovenian translation for: ' + json.dumps(missing, ensure_ascii=False, indent=1))
    lang = os.path.join(ROOT, 'languages')
    os.makedirs(lang, exist_ok=True)
    write_po(os.path.join(lang, DOMAIN + '.pot'), found, False)
    write_po(os.path.join(lang, DOMAIN + '-sl_SI.po'), found, True)
    header = ('Project-Id-Version: Degriz EU Guarantee 1.0\nContent-Type: text/plain; charset=UTF-8\n'
              'Content-Transfer-Encoding: 8bit\nLanguage: sl_SI\n'
              'Plural-Forms: nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3);\n')
    pairs = {'': header}
    pairs.update({k: TRANSLATIONS[k] for k in found})
    write_mo(os.path.join(lang, DOMAIN + '-sl_SI.mo'), pairs)
    print(len(found), 'strings')


main()
