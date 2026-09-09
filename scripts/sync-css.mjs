// Keep browser CSS exports identical to the PHP generator. Prints an apply_patch
// patch; it does not modify files. Run after changing theme_studio_css().
import {readFileSync} from 'node:fs';
const base = new URL('../source/theme.studio/usr/local/emhttp/plugins/theme.studio/', import.meta.url);
const php = readFileSync(new URL('include/theme.php', base), 'utf8').replace(/\r\n/g, '\n');
const path = new URL('assets/theme-studio.js', base);
const js = readFileSync(path, 'utf8').replace(/\r\n/g, '\n');
const css = php.match(/return <<<CSS\n([\s\S]*?)\nCSS;/)[1];
const template = css.replace(/\\/g, '\\\\').replace(/`/g, '\\`')
  .replace(/\{\$t\['(\w+)'\]\}/g, '${theme.$1}')
  .replace(/\{\$(\w+)\}/g, (_, name) => '${' + name + '}');
const animationStart = js.indexOf('    var animation =', js.indexOf('  function cssForTheme('));
const returnStart = js.indexOf('    return ', animationStart);
const nameStart = js.indexOf('    var name =', animationStart);
const start = nameStart >= 0 && nameStart < returnStart ? nameStart : returnStart;
const end = js.indexOf('\n  }', start);
const replacement = '    var name = theme.name.replace(/\\*\\/|[\\r\\n]/g, "");\n    return `' + template + '`;';
const old = js.slice(start, end);
if (old !== replacement) console.log('*** Begin Patch\n*** Update File: ' + path.pathname.replace(/^\/(\w:)/, '$1') + '\n@@\n' + old.split('\n').map(l => '-' + l).join('\n') + '\n' + replacement.split('\n').map(l => '+' + l).join('\n') + '\n*** End Patch');
